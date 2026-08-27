<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum AgencyRiskLevel: string
{
    use HasOptions;

    case NotAssessed = 'not_assessed';
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';

    public function label(): string
    {
        return str($this->value)->replace('_', ' ')->title()->toString();
    }
}
