<?php
namespace App\Enums;

enum AccommodationType: string
{
    case Resort = 'resort';
    case Guesthouse = 'guesthouse';
    case Liveaboard = 'liveaboard';

    public function label(): string
    {
        return match ($this) {
            self::Resort => 'Resort', self::Guesthouse => 'Guesthouse', self::Liveaboard => 'Liveaboard',
        };
    }
}
