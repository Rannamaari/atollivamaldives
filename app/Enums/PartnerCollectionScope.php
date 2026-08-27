<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum PartnerCollectionScope: string
{
    use HasOptions;

    case Suppliers = 'suppliers';
    case AgencyPartners = 'agency_partners';
    case Both = 'both';

    public function label(): string
    {
        return match ($this) {
            self::Suppliers => 'Suppliers only',
            self::AgencyPartners => 'Agency partners only',
            self::Both => 'Suppliers and agency partners',
        };
    }
}
