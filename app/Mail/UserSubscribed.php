<?php

namespace App\Mail;

use App\Models\CompanySubscription;
use App\Models\PaymentMethod;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserSubscribed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        protected CompanySubscription $companySubscription,
    ){}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Easeweldo - Your Subscription is Received!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription-success',
            with: [
                'company' => $this->companySubscription->company,
                'subscription' => $this->companySubscription->subscription,
                'company_subscription' => $this->companySubscription,
                'payment_methods' => PaymentMethod::where('status', PaymentMethod::STATUS_ACTIVE)->get()
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
