<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum ContactMethod: string
{
    use HasOptions;

    case Email = 'email';
    case Telephone = 'telephone';
    case WhatsApp = 'whatsapp';
    case Meeting = 'meeting';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::WhatsApp => 'WhatsApp',
            default => str($this->value)->title()->toString(),
        };
    }
}
