<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum DocumentType: string
{
    use HasOptions;

    case SupplierAgreement = 'supplier_agreement';
    case RateSheet = 'rate_sheet';
    case SpecialOffer = 'special_offer';
    case PropertyLicence = 'property_licence';
    case AgencyLicence = 'agency_licence';
    case AgencyAgreement = 'agency_agreement';
    case CompanyRegistration = 'company_registration';
    case TgstCertificate = 'tgst_certificate';
    case MarketingMaterial = 'marketing_material';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::TgstCertificate => 'TGST certificate',
            default => str($this->value)->replace('_', ' ')->title()->toString(),
        };
    }
}
