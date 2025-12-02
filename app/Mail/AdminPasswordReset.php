<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminPasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    public $admin;
    public $newPassword;

    public function __construct($admin, $newPassword)
    {
        $this->admin = $admin;
        $this->newPassword = $newPassword;
    }

    public function build()
    {
        return $this->subject('New Admin Password')
                    ->view('emails.admin_password_reset');
    }
}
