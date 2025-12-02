<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use App\Models\TenantModel as Tenant;
use App\Models\RentCycle;

class RentRenewed extends Mailable
{
    use Queueable, SerializesModels;

    public $tenant;
    public $rentCycle;

    public function __construct(Tenant $tenant, RentCycle $rentCycle)
    {
        $this->tenant = $tenant;
        $this->rentCycle = $rentCycle;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Rent Renewal Confirmation',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.rent_renewed',
            with: [
                'tenant' => $this->tenant,
                'rentCycle' => $this->rentCycle
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
