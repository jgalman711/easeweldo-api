<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetTemporaryPassword extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(protected User $user)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Temporary Password Reset',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.temporary-password-reset',
            with: [
                'temporaryPassword' => $this->user->temporary_password,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
