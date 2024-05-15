<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserRegistered extends Mailable
{
    use Queueable, SerializesModels;

    protected $title;

    public function __construct(protected Company $company, protected User $user)
    {
        $this->title =  "Welcome aboard, $user->full_name!";
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-success',
            with: [
                'title' => $this->title,
                'company' => $this->company,
                'user' => $this->user,
                'temporaryPassword' => $this->user->temporary_password,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
