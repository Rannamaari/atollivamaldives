<?php

namespace App\Services\OperationsHub;

use App\Enums\ActivityEventType;
use App\Enums\CommunicationStatus;
use App\Models\AgencyPartner;
use App\Models\Communication;
use App\Models\Supplier;

class CommunicationLogger
{
    public function __construct(
        private readonly FollowUpScheduler $followUpScheduler,
        private readonly ActivityLogger $activityLogger,
    ) {}

    public function finalize(Communication $communication): Communication
    {
        if (! $communication->logged_by) {
            $communication->logged_by = auth()->id();
        }

        if (! $communication->occurred_at && $communication->status !== CommunicationStatus::Draft) {
            $communication->occurred_at = now();
        }

        $communication->save();

        $this->touchRelatedEntity($communication);
        $task = $this->followUpScheduler->scheduleCommunicationFollowUp($communication);

        $this->activityLogger->log(
            ActivityEventType::CommunicationLogged,
            'Communication logged',
            $communication->subject ?: $communication->channel?->label(),
            supplier: $communication->supplier,
            agencyPartner: $communication->agencyPartner,
            rateRequest: $communication->rateRequest,
            communication: $communication,
            task: $task,
        );

        return $communication->refresh();
    }

    private function touchRelatedEntity(Communication $communication): void
    {
        $payload = [
            'last_contacted_at' => $communication->occurred_at ?? now(),
            'next_follow_up_at' => $communication->next_follow_up_at,
        ];

        if ($communication->supplier instanceof Supplier) {
            $communication->supplier->update(array_filter($payload, fn ($value) => $value !== null));
        }

        if ($communication->agencyPartner instanceof AgencyPartner) {
            $communication->agencyPartner->update(array_filter($payload, fn ($value) => $value !== null));
        }
    }
}
