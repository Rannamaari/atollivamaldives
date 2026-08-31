<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum CommunicationChannel: string
{
    use HasOptions;

    case EmailDraft = 'email_draft';
    case EmailSentManually = 'email_sent_manually';
    case EmailSentAutomatically = 'email_sent_automatically';
    case EmailReceived = 'email_received';
    case PhoneCall = 'phone_call';
    case WhatsApp = 'whatsapp';
    case Meeting = 'meeting';
    case InternalNote = 'internal_note';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::WhatsApp => 'WhatsApp',
            default => str($this->value)->replace('_', ' ')->title()->toString(),
        };
    }
}
