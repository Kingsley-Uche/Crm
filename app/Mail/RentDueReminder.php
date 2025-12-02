<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\RentAccount;
use App\Models\RentCycle;

class RentDueReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $account;
    public $cycle;

    /**
     * Create a new message instance.
     */
    public function __construct(RentAccount $account, RentCycle $cycle)
    {
        $this->account = $account;
        $this->cycle = $cycle;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Rent Due Reminder',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.rent_due',
            with: [
                'account' => $this->account,
                'cycle' => $this->cycle,
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
