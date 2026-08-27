<?php

namespace App\Services\OperationsHub;

use App\Enums\ActivityEventType;
use App\Enums\RateRequestStatus;
use App\Enums\SupplierPartnershipStatus;
use App\Models\RateRequest;
use Illuminate\Support\Facades\DB;

class RateRequestWorkflow
{
    public function __construct(
        private readonly FollowUpScheduler $followUpScheduler,
        private readonly ActivityLogger $activityLogger,
    ) {}

    public function markReady(RateRequest $rateRequest): RateRequest
    {
        $rateRequest->update([
            'status' => RateRequestStatus::ReadyToSend,
            'drafted_at' => $rateRequest->drafted_at ?? now(),
        ]);

        $this->activityLogger->log(
            ActivityEventType::RateRequestAction,
            'Rate request marked ready',
            $rateRequest->request_title,
            supplier: $rateRequest->supplier,
            rateRequest: $rateRequest,
        );

        return $rateRequest->refresh();
    }

    public function markSent(RateRequest $rateRequest): RateRequest
    {
        return DB::transaction(function () use ($rateRequest): RateRequest {
            if (! $rateRequest->sent_at) {
                $rateRequest->forceFill([
                    'status' => RateRequestStatus::Sent,
                    'sent_at' => now(),
                    'sent_by' => auth()->id(),
                    'next_follow_up_at' => $this->followUpScheduler
                        ->nextBusinessDate((int) config('operations_hub.follow_up.supplier_rate_request_business_days'))
                        ->setTime(9, 0),
                ])->save();

                $rateRequest->supplier?->update([
                    'last_contacted_at' => now(),
                    'next_follow_up_at' => $rateRequest->next_follow_up_at,
                    'partnership_status' => SupplierPartnershipStatus::RateRequestSent,
                ]);
            }

            $task = $this->followUpScheduler->scheduleSupplierRateFollowUp($rateRequest);

            $this->activityLogger->log(
                ActivityEventType::RateRequestAction,
                'Rate request marked sent',
                'The rate request was marked as sent manually.',
                supplier: $rateRequest->supplier,
                rateRequest: $rateRequest,
                task: $task,
            );

            return $rateRequest->refresh();
        });
    }

    public function recordResponse(RateRequest $rateRequest, bool $ratesReceived = false, bool $agreementReceived = false): RateRequest
    {
        $status = $agreementReceived
            ? RateRequestStatus::AgreementReceived
            : ($ratesReceived ? RateRequestStatus::RatesReceived : RateRequestStatus::ResponseReceived);

        $rateRequest->update([
            'status' => $status,
            'response_received_at' => now(),
            'rates_received' => $ratesReceived || $rateRequest->rates_received,
            'agreement_received' => $agreementReceived || $rateRequest->agreement_received,
        ]);

        $rateRequest->supplier?->update([
            'last_contacted_at' => now(),
            'partnership_status' => $agreementReceived
                ? SupplierPartnershipStatus::AgreementPending
                : ($ratesReceived ? SupplierPartnershipStatus::RatesReceived : SupplierPartnershipStatus::AwaitingResponse),
        ]);

        $this->activityLogger->log(
            ActivityEventType::RateRequestAction,
            'Rate request response recorded',
            'A response was logged for the rate request.',
            supplier: $rateRequest->supplier,
            rateRequest: $rateRequest,
        );

        return $rateRequest->refresh();
    }
}
