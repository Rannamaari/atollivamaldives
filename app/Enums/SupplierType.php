<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum SupplierType: string
{
    use HasOptions;

    case Resort = 'resort';
    case Guesthouse = 'guesthouse';
    case CityHotel = 'city_hotel';
    case Liveaboard = 'liveaboard';
    case Dmc = 'dmc';
    case TransferProvider = 'transfer_provider';
    case ExcursionProvider = 'excursion_provider';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Resort => 'Resort',
            self::Guesthouse => 'Guesthouse',
            self::CityHotel => 'City hotel',
            self::Liveaboard => 'Liveaboard',
            self::Dmc => 'DMC',
            self::TransferProvider => 'Transfer provider',
            self::ExcursionProvider => 'Excursion provider',
            self::Other => 'Other',
        };
    }
}
