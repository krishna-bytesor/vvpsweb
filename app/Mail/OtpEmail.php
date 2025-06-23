<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $email;
    protected $otp;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $otp)
    {
        $this->email = $email;
        $this->otp = $otp;
    }

    public function build()
    {
        return $this->to($this->email)
                ->from(config('mail.from.address'), config('mail.from.name'))
                ->subject('Email Registration OTP')
                ->markdown('emails.email_otp', ['otp' => $this->otp, 'email'=> $this->email]);
    }
}
