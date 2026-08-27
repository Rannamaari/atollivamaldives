<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum SupplierPartnershipStatus: string
{
    use HasOptions;

    case NotContacted = 'not_contacted';
    case IntroductionPrepared = 'introduction_prepared';
    case RateRequestSent = 'rate_request_sent';
    case AwaitingResponse = 'awaiting_response';
    case FollowUpRequired = 'follow_up_required';
    case RatesReceived = 'rates_received';
    case RatesUnderReview = 'rates_under_review';
    case AgreementPending = 'agreement_pending';
    case ActivePartner = 'active_partner';
    case Declined = 'declined';
    case TemporarilyInactive = 'temporarily_inactive';
    case ContractExpired = 'contract_expired';

    public function label(): string
    {
        return str($this->value)->replace('_', ' ')->title()->toString();
    }
}
