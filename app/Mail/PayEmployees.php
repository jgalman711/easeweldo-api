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

class PayEmployees extends Mailable
{
    use Queueable, SerializesModels;

    protected $company;

    protected $bank;

    protected $disbursement;

    public function __construct(Company $company, Bank $bank, Period $disbursement)
    {
        $this->company = $company;
        $this->bank = $bank;
        $this->disbursement = $disbursement;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Submission of Employee Details for Salary Disbursement',
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
                'companyName' => $this->company->name,
                'bankProvider' => $this->bank->name,
                'bankProviderBranch' => $this->bank->branch,
                'salaryDate' => $this->disbursement->salary_date,
                'companyContactNumber' => $this->company->mobile_number,
                'companyEmail' => $this->company->email_address,
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
