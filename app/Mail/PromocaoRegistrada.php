<?php

namespace App\Mail;

use App\Models\Promocao;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PromocaoRegistrada extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Promocao $promocao) {}

    public function build()
    {
        return $this->subject('Você foi promovido(a)! 🎉')->view('emails.promocao');
    }
}
