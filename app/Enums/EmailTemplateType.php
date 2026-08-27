<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum EmailTemplateType: string
{
    use HasOptions;

    case SupplierIntroduction = 'supplier_introduction';
    case RequestB2BRates = 'request_b2b_rates';
    case RequestSupplierAgreement = 'request_supplier_agreement';
    case FirstSupplierFollowUp = 'first_supplier_follow_up';
    case SecondSupplierFollowUp = 'second_supplier_follow_up';
    case RequestUpdatedRates = 'request_updated_rates';
    case RateExpiryReminder = 'rate_expiry_reminder';
    case RequestSpecialOffers = 'request_special_offers';
    case RequestApprovedPhotographs = 'request_approved_photographs';
    case AgencyIntroduction = 'agency_introduction';
    case AgencyPartnershipInvitation = 'agency_partnership_invitation';
    case FirstAgencyFollowUp = 'first_agency_follow_up';
    case SecondAgencyFollowUp = 'second_agency_follow_up';
    case RequestAgencyDocuments = 'request_agency_documents';
    case AgreementFollowUp = 'agreement_follow_up';

    public function label(): string
    {
        return str($this->value)->replace('_', ' ')->title()->toString();
    }
}
