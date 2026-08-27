<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum TaskType: string
{
    use HasOptions;

    case SupplierFollowUp = 'supplier_follow_up';
    case AgencyFollowUp = 'agency_follow_up';
    case RateReview = 'rate_review';
    case AgreementReview = 'agreement_review';
    case DocumentRenewal = 'document_renewal';
    case Call = 'call';
    case Meeting = 'meeting';
    case General = 'general';

    public function label(): string
    {
        return str($this->value)->replace('_', ' ')->title()->toString();
    }
}
