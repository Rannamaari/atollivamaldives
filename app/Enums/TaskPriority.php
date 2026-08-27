<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum TaskPriority: string
{
    use HasOptions;

    case Low = 'low';
    case Normal = 'normal';
    case High = 'high';
    case Urgent = 'urgent';

    public function label(): string
    {
        return str($this->value)->title()->toString();
    }
}
