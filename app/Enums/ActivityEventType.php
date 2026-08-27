<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum ActivityEventType: string
{
    use HasOptions;

    case Created = 'created';
    case Updated = 'updated';
    case StatusChanged = 'status_changed';
    case CommunicationLogged = 'communication_logged';
    case RateRequestAction = 'rate_request_action';
    case TaskCreated = 'task_created';
    case TaskUpdated = 'task_updated';
    case TaskCompleted = 'task_completed';
    case NoteAdded = 'note_added';
    case DocumentUploaded = 'document_uploaded';

    public function label(): string
    {
        return str($this->value)->replace('_', ' ')->title()->toString();
    }
}
