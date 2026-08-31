<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AgencyCampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $subjectLine,
        public readonly string $bodyText,
        public readonly ?string $replyToAddress = null,
    ) {}

    public function build(): self
    {
        $mail = $this->subject($this->subjectLine)
            ->view('emails.agency-campaign')
            ->with([
                'bodyText' => $this->bodyText,
            ]);

        if (filled($this->replyToAddress)) {
            $mail->replyTo($this->replyToAddress);
        }

        return $mail;
    }
}
