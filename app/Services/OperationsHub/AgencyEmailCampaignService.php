<?php

namespace App\Services\OperationsHub;

use App\Enums\AgencyEmailCampaignStatus;
use App\Enums\AgencyEmailRecipientStatus;
use App\Models\AgencyContact;
use App\Models\AgencyEmailCampaign;
use App\Models\AgencyEmailCampaignRecipient;
use App\Models\AgencyPartner;
use App\Models\PartnerCollection;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AgencyEmailCampaignService
{
    public function syncRecipientsAndSchedule(AgencyEmailCampaign $campaign): void
    {
        $targets = $this->buildTargets($campaign);
        $existingRecipients = $campaign->recipients()->get()->keyBy(fn (AgencyEmailCampaignRecipient $recipient) => $this->recipientKey($recipient));
        $targetKeys = [];

        foreach ($targets as $target) {
            $recipient = $existingRecipients->get($target['key']) ?? new AgencyEmailCampaignRecipient([
                'campaign_id' => $campaign->id,
            ]);

            $targetKeys[] = $target['key'];

            if ($recipient->status === AgencyEmailRecipientStatus::Sent) {
                continue;
            }

            $hasEmail = filled($target['recipient_email'] ?? null);

            $recipient->fill([
                'agency_partner_id' => $target['agency_partner_id'] ?? null,
                'agency_contact_id' => $target['agency_contact_id'] ?? null,
                'recipient_email' => $target['recipient_email'] ?? null,
                'recipient_name' => $target['recipient_name'] ?? null,
                'status' => $hasEmail
                    ? AgencyEmailRecipientStatus::Scheduled->value
                    : AgencyEmailRecipientStatus::Skipped->value,
                'failure_reason' => $hasEmail ? null : 'No email address available.',
            ]);
            $recipient->save();
        }

        $campaign->recipients()
            ->get()
            ->filter(fn (AgencyEmailCampaignRecipient $recipient) => ! in_array($this->recipientKey($recipient), $targetKeys, true))
            ->filter(fn (AgencyEmailCampaignRecipient $recipient) => ! in_array($recipient->status, [AgencyEmailRecipientStatus::Sent, AgencyEmailRecipientStatus::Processing], true))
            ->each
            ->delete();

        $this->scheduleRecipients($campaign->fresh('recipients'));
    }

    public function scheduleRecipients(AgencyEmailCampaign $campaign): void
    {
        $startDate = $campaign->start_date ?: now()->toDateString();
        $baseTime = Carbon::parse($startDate.' '.$campaign->send_window_starts_at);
        $dailyLimit = max(1, min((int) $campaign->daily_limit, (int) config('operations_hub.campaigns.hard_daily_limit', 20)));
        $intervalMinutes = max((int) config('operations_hub.campaigns.minimum_interval_minutes', 5), (int) $campaign->interval_minutes);

        $pendingRecipients = $campaign->recipients()
            ->whereNotIn('status', [
                AgencyEmailRecipientStatus::Sent->value,
                AgencyEmailRecipientStatus::Processing->value,
                AgencyEmailRecipientStatus::Cancelled->value,
                AgencyEmailRecipientStatus::Failed->value,
            ])
            ->orderBy('id')
            ->get();

        foreach ($pendingRecipients->values() as $index => $recipient) {
            if ($recipient->status === AgencyEmailRecipientStatus::Skipped) {
                continue;
            }

            $dayOffset = intdiv($index, $dailyLimit);
            $slotInDay = $index % $dailyLimit;
            $scheduledFor = $baseTime->copy()
                ->addDays($dayOffset)
                ->addMinutes($slotInDay * $intervalMinutes);

            $recipient->update([
                'scheduled_for' => $scheduledFor,
                'status' => AgencyEmailRecipientStatus::Scheduled,
                'failure_reason' => null,
            ]);
        }
    }

    public function startCampaign(AgencyEmailCampaign $campaign): void
    {
        $this->syncRecipientsAndSchedule($campaign);

        $campaign->update([
            'status' => AgencyEmailCampaignStatus::Scheduled,
            'started_at' => now(),
            'paused_at' => null,
            'stopped_at' => null,
            'completed_at' => null,
        ]);
    }

    public function pauseCampaign(AgencyEmailCampaign $campaign): void
    {
        $campaign->update([
            'status' => AgencyEmailCampaignStatus::Paused,
            'paused_at' => now(),
        ]);
    }

    public function stopCampaign(AgencyEmailCampaign $campaign): void
    {
        $campaign->update([
            'status' => AgencyEmailCampaignStatus::Stopped,
            'stopped_at' => now(),
        ]);

        $campaign->recipients()
            ->whereIn('status', [AgencyEmailRecipientStatus::Scheduled->value, AgencyEmailRecipientStatus::Processing->value])
            ->update([
                'status' => AgencyEmailRecipientStatus::Cancelled->value,
                'failure_reason' => 'Campaign stopped.',
            ]);
    }

    public function markCompletedIfFinished(AgencyEmailCampaign $campaign): void
    {
        $remaining = $campaign->recipients()
            ->whereIn('status', [AgencyEmailRecipientStatus::Scheduled->value, AgencyEmailRecipientStatus::Processing->value])
            ->count();

        if ($remaining === 0 && $campaign->status !== AgencyEmailCampaignStatus::Stopped) {
            $campaign->update([
                'status' => AgencyEmailCampaignStatus::Completed,
                'completed_at' => now(),
            ]);
        }
    }

    protected function buildTargets(AgencyEmailCampaign $campaign): Collection
    {
        $targets = collect();

        $agencyPartners = $this->resolveAgencyPartners($campaign);

        foreach ($agencyPartners as $agencyPartner) {
            $contact = $agencyPartner->contacts()
                ->where('is_active', true)
                ->orderByDesc('is_primary')
                ->orderBy('id')
                ->first();

            $targets->push([
                'key' => 'agency:'.$agencyPartner->id,
                'agency_partner_id' => $agencyPartner->id,
                'agency_contact_id' => $contact?->id,
                'recipient_email' => $contact?->email ?: $agencyPartner->email,
                'recipient_name' => $contact?->full_name ?: ($agencyPartner->trading_name ?: $agencyPartner->legal_company_name),
            ]);
        }

        $contactIds = collect($campaign->agency_contact_ids ?: [])->filter()->map(fn ($id) => (int) $id)->unique()->values();
        if ($contactIds->isNotEmpty()) {
            $contacts = AgencyContact::query()
                ->with('agencyPartner')
                ->whereIn('id', $contactIds)
                ->where('is_active', true)
                ->get();

            foreach ($contacts as $contact) {
                $targets->push([
                    'key' => 'contact:'.$contact->id,
                    'agency_partner_id' => $contact->agency_partner_id,
                    'agency_contact_id' => $contact->id,
                    'recipient_email' => $contact->email ?: $contact->agencyPartner?->email,
                    'recipient_name' => $contact->full_name,
                ]);
            }
        }

        foreach ($this->parseManualRecipients((string) ($campaign->manual_recipients ?? '')) as $index => $manualRecipient) {
            $targets->push([
                'key' => 'manual:'.$index.':'.md5(strtolower($manualRecipient['recipient_email'])),
                'agency_partner_id' => null,
                'agency_contact_id' => null,
                'recipient_email' => $manualRecipient['recipient_email'],
                'recipient_name' => $manualRecipient['recipient_name'],
            ]);
        }

        return $targets
            ->filter(fn (array $target) => filled($target['recipient_email'] ?? null) || filled($target['recipient_name'] ?? null))
            ->unique(fn (array $target) => strtolower(trim((string) ($target['recipient_email'] ?? ''))))
            ->values();
    }

    protected function resolveAgencyPartners(AgencyEmailCampaign $campaign): Collection
    {
        $directIds = collect($campaign->agency_partner_ids ?: []);
        $collectionIds = collect($campaign->partner_collection_ids ?: []);

        $fromCollections = AgencyPartner::query()
            ->whereHas('collections', fn ($query) => $query->whereIn('partner_collections.id', $collectionIds))
            ->pluck('agency_partners.id');

        $agencyIds = $directIds
            ->merge($fromCollections)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($agencyIds->isEmpty()) {
            return collect();
        }

        return AgencyPartner::query()
            ->with('contacts')
            ->whereIn('id', $agencyIds)
            ->where('is_active', true)
            ->orderByRaw("coalesce(nullif(trading_name, ''), legal_company_name)")
            ->get();
    }

    protected function parseManualRecipients(string $manualRecipients): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $manualRecipients) ?: [];
        $parsed = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            if (preg_match('/^(.*?)<\s*([^>]+)\s*>$/', $line, $matches) === 1) {
                $parsed[] = [
                    'recipient_name' => trim($matches[1]) ?: null,
                    'recipient_email' => trim($matches[2]),
                ];

                continue;
            }

            $parsed[] = [
                'recipient_name' => null,
                'recipient_email' => $line,
            ];
        }

        return $parsed;
    }

    protected function recipientKey(AgencyEmailCampaignRecipient $recipient): string
    {
        if ($recipient->agency_contact_id) {
            return 'contact:'.$recipient->agency_contact_id;
        }

        if ($recipient->agency_partner_id) {
            return 'agency:'.$recipient->agency_partner_id;
        }

        return 'manual:'.md5(strtolower(trim((string) $recipient->recipient_email)));
    }
}
