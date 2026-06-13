<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailTeste extends Mailable
{
    use Queueable, SerializesModels;

    public function build()
    {
        return $this->subject('E-mail de teste — ' . config('app.name'))->view('emails.teste');
    }
}
