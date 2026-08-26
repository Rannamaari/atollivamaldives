<?php

namespace App\Enums;

enum AccommodationType: string
{
    case Resort = 'resort';
    case Guesthouse = 'guesthouse';
    case Liveaboard = 'liveaboard';
    case CityHotel = 'city_hotel';
    case Package = 'package';

    public function label(): string
    {
        return match ($this) {
            self::Resort => 'Resort',
            self::Guesthouse => 'Guest Houses',
            self::Liveaboard => 'Liveaboards',
            self::CityHotel => 'City Hotels',
            self::Package => 'Packages',
        };
    }
}
