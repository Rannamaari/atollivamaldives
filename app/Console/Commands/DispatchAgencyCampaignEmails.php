<?php

namespace App\Console\Commands;

use App\Enums\AgencyEmailCampaignStatus;
use App\Enums\AgencyEmailRecipientStatus;
use App\Jobs\SendAgencyCampaignEmailJob;
use App\Models\AgencyEmailCampaign;
use App\Models\AgencyEmailCampaignRecipient;
use Illuminate\Console\Command;

class DispatchAgencyCampaignEmails extends Command
{
    protected $signature = 'agency-campaigns:dispatch-due';

    protected $description = 'Dispatch due agency campaign emails while respecting campaign daily limits.';

    public function handle(): int
    {
        $campaigns = AgencyEmailCampaign::query()
            ->whereIn('status', [AgencyEmailCampaignStatus::Scheduled->value, AgencyEmailCampaignStatus::Sending->value])
            ->get();

        foreach ($campaigns as $campaign) {
            if ($campaign->recipients()->where('status', AgencyEmailRecipientStatus::Processing->value)->exists()) {
                continue;
            }

            $sentToday = $campaign->recipients()
                ->whereDate('sent_at', today())
                ->count();

            $remainingToday = max(0, min((int) $campaign->daily_limit, (int) config('operations_hub.campaigns.hard_daily_limit', 20)) - $sentToday);

            if ($remainingToday < 1) {
                continue;
            }

            $lastSentAt = $campaign->recipients()
                ->whereNotNull('sent_at')
                ->latest('sent_at')
                ->value('sent_at');

            if ($lastSentAt && now()->diffInMinutes($lastSentAt) < max(1, (int) $campaign->interval_minutes)) {
                continue;
            }

            $campaign->update([
                'status' => AgencyEmailCampaignStatus::Sending,
            ]);

            $recipient = $campaign->recipients()
                ->where('status', AgencyEmailRecipientStatus::Scheduled->value)
                ->whereNotNull('recipient_email')
                ->where('scheduled_for', '<=', now())
                ->orderBy('scheduled_for')
                ->first();

            if (! $recipient) {
                continue;
            }

            $updated = AgencyEmailCampaignRecipient::query()
                ->whereKey($recipient->id)
                ->where('status', AgencyEmailRecipientStatus::Scheduled->value)
                ->update([
                    'status' => AgencyEmailRecipientStatus::Processing->value,
                ]);

            if ($updated) {
                SendAgencyCampaignEmailJob::dispatch($recipient->id)->onQueue('default');
            }
        }

        return self::SUCCESS;
    }
}
