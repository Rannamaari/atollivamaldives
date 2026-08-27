<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum AgencyPartnershipStatus: string
{
    use HasOptions;

    case ProspectIdentified = 'prospect_identified';
    case IntroductionPrepared = 'introduction_prepared';
    case IntroductionSent = 'introduction_sent';
    case FollowUpDue = 'follow_up_due';
    case Interested = 'interested';
    case VerificationRequired = 'verification_required';
    case AgreementSent = 'agreement_sent';
    case AgreementSigned = 'agreement_signed';
    case ActivePartner = 'active_partner';
    case Dormant = 'dormant';
    case Declined = 'declined';
    case Suspended = 'suspended';

    public function label(): string
    {
        return str($this->value)->replace('_', ' ')->title()->toString();
    }
}
