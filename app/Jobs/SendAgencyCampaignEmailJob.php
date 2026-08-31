<?php

namespace App\Jobs;

use App\Enums\CommunicationChannel;
use App\Enums\CommunicationDirection;
use App\Enums\CommunicationStatus;
use App\Mail\AgencyCampaignMail;
use App\Models\AgencyEmailCampaign;
use App\Models\AgencyEmailCampaignRecipient;
use App\Models\Communication;
use App\Services\OperationsHub\AgencyEmailCampaignService;
use App\Services\OperationsHub\TemplateRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;
use App\Enums\AgencyEmailRecipientStatus;

class SendAgencyCampaignEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public readonly int $recipientId) {}

    public function handle(TemplateRenderer $renderer, AgencyEmailCampaignService $campaignService): void
    {
        $recipient = AgencyEmailCampaignRecipient::query()
            ->with(['campaign.emailTemplate', 'agencyPartner', 'agencyContact'])
            ->findOrFail($this->recipientId);

        $campaign = $recipient->campaign;

        if (! $campaign || ! filled($recipient->recipient_email)) {
            $recipient->update([
            'status' => AgencyEmailRecipientStatus::Skipped,
            'failure_reason' => 'Missing campaign or recipient email.',
        ]);

            return;
        }

        $agency = $recipient->agencyPartner;
        $contact = $recipient->agencyContact;

        $subject = $campaign->subject_override;
        $body = $campaign->body_override;

        if (blank($subject) || blank($body)) {
            $rendered = $campaign->emailTemplate
                ? $renderer->render($campaign->emailTemplate, [
                    'agency' => $agency,
                    'contact_name' => $contact?->full_name ?: $recipient->recipient_name,
                ])
                : ['subject' => '', 'body' => ''];

            $subject = $campaign->subject_override ?: ($rendered['subject'] ?? 'Greetings from Atolliva Maldives');
            $body = $campaign->body_override ?: ($rendered['body'] ?? 'Greetings from Atolliva Maldives.');
        }

        Mail::to($recipient->recipient_email)->send(
            (new AgencyCampaignMail(
                subjectLine: $subject,
                bodyText: $body,
                replyToAddress: $campaign->reply_to_email,
            ))->from(
                $campaign->sender_email ?: config('operations_hub.company.email'),
                $campaign->sender_name ?: config('operations_hub.company.name')
            )
        );

        $communication = Communication::create([
            'agency_partner_id' => $agency?->id,
            'agency_contact_id' => $contact?->id,
            'direction' => CommunicationDirection::Outbound,
            'channel' => CommunicationChannel::EmailSentAutomatically,
            'status' => CommunicationStatus::Completed,
            'recipient' => $recipient->recipient_email,
            'subject' => $subject,
            'body' => $body,
            'occurred_at' => now(),
            'logged_by' => $campaign->created_by,
            'follow_up_required' => true,
            'next_follow_up_at' => now()->addWeekdays((int) config('operations_hub.follow_up.agency_intro_business_days', 4))->setTime(10, 0),
        ]);

        $recipient->update([
            'communication_id' => $communication->id,
            'status' => AgencyEmailRecipientStatus::Sent,
            'sent_at' => now(),
            'attempts' => $recipient->attempts + 1,
            'failure_reason' => null,
        ]);

        $campaignService->markCompletedIfFinished($campaign->fresh());
    }

    public function failed(Throwable $exception): void
    {
        $recipient = AgencyEmailCampaignRecipient::find($this->recipientId);

        if (! $recipient) {
            return;
        }

        $recipient->update([
            'status' => AgencyEmailRecipientStatus::Failed,
            'failure_reason' => str($exception->getMessage())->limit(1000)->toString(),
            'attempts' => $recipient->attempts + 1,
        ]);
    }
}
