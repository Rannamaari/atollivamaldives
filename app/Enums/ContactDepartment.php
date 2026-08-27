<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum ContactDepartment: string
{
    use HasOptions;

    case Sales = 'sales';
    case Reservations = 'reservations';
    case Accounts = 'accounts';
    case Management = 'management';
    case Marketing = 'marketing';
    case Other = 'other';

    public function label(): string
    {
        return str($this->value)->title()->toString();
    }
}
