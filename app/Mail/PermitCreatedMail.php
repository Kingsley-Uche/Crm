<?php

namespace App\Mail;

use App\Models\ParkPermits;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;

class PermitCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $permit;

    /**
     * Create a new message instance.
     */
    public function __construct(ParkPermits $permit)
    {
        $this->permit = $permit;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Parking Permit Has Been Created'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.permit_created'
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
