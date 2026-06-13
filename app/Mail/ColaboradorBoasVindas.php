<?php

namespace App\Mail;

use App\Models\Colaborador;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ColaboradorBoasVindas extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Colaborador $colaborador, public ?string $emailLogin = null) {}

    public function build()
    {
        return $this->subject('Bem-vindo(a) à ' . ($this->colaborador->empresa->nome ?? config('app.name')))
            ->view('emails.boas-vindas');
    }
}
