<?php

namespace App\Mail;

use App\Models\Bank;
use App\Models\Company;
use App\Models\Period;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class PayEmployees extends Mailable
{
    use Queueable, SerializesModels;

    protected $company;

    protected $bank;

    protected $disbursement;

    protected $title;

    public function __construct(Company $company, Bank $bank, Period $disbursement)
    {
        $this->company = $company;
        $this->bank = $bank;
        $this->disbursement = $disbursement;
        $this->title =  $company->name . " Salary Disbursement - " . $disbursement->salary_date;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.disbursement.via-bank',
            with: [
                'title' => $this->title,
                'company' => $this->company,
                'bank' => $this->bank,
                'disbursement' => $this->disbursement,
                'sender' => Auth::user()->load('employee')
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
