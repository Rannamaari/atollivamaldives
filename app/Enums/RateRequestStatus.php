<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum RateRequestStatus: string
{
    use HasOptions;

    case Draft = 'draft';
    case ReadyToSend = 'ready_to_send';
    case Sent = 'sent';
    case AwaitingResponse = 'awaiting_response';
    case FirstFollowUpDue = 'first_follow_up_due';
    case FirstFollowUpSent = 'first_follow_up_sent';
    case SecondFollowUpDue = 'second_follow_up_due';
    case SecondFollowUpSent = 'second_follow_up_sent';
    case ResponseReceived = 'response_received';
    case RatesReceived = 'rates_received';
    case AgreementReceived = 'agreement_received';
    case Completed = 'completed';
    case Declined = 'declined';
    case Closed = 'closed';

    public function label(): string
    {
        return str($this->value)->replace('_', ' ')->title()->toString();
    }
}
