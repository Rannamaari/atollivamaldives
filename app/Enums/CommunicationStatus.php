<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum CommunicationStatus: string
{
    use HasOptions;

    case Draft = 'draft';
    case Prepared = 'prepared';
    case SentManually = 'sent_manually';
    case Received = 'received';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return str($this->value)->replace('_', ' ')->title()->toString();
    }
}
