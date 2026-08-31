<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum AgencyEmailRecipientStatus: string
{
    use HasOptions;

    case Scheduled = 'scheduled';
    case Processing = 'processing';
    case Sent = 'sent';
    case Failed = 'failed';
    case Skipped = 'skipped';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return str($this->value)->replace('_', ' ')->title()->toString();
    }
}
