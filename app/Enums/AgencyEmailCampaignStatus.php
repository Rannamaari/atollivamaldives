<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum AgencyEmailCampaignStatus: string
{
    use HasOptions;

    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case Sending = 'sending';
    case Paused = 'paused';
    case Completed = 'completed';
    case Stopped = 'stopped';

    public function label(): string
    {
        return str($this->value)->replace('_', ' ')->title()->toString();
    }
}
