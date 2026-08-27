<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum CommunicationDirection: string
{
    use HasOptions;

    case Inbound = 'inbound';
    case Outbound = 'outbound';
    case Internal = 'internal';

    public function label(): string
    {
        return str($this->value)->title()->toString();
    }
}
