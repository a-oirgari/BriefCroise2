<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $colocationName,
        public string $inviteUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Invitation - Colocation {$this->colocationName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invitation',
        );
    }
}