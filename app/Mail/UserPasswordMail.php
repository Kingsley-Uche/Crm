<?php

namespace App\Mail;

use App\Models\AdminModel as User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $receiver;
    public $password;

    /**
     * Create a new message instance.
     */
    public function __construct(User $receiver, string $password)
    {
        $this->receiver = $receiver;
        $this->password = $password;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your CTR Account Password')
                    ->markdown('emails.user_password');
    }
}
