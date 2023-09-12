<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(protected User $user)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Easeweldo - You Are Now Registered',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-success',
            with: [
                'temporaryPassword' => $this->user->temporary_password
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
