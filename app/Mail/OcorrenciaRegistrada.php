<?php

namespace App\Mail;

use App\Models\Ocorrencia;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OcorrenciaRegistrada extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Ocorrencia $ocorrencia) {}

    public function build()
    {
        return $this->subject('Registro de ocorrência')->view('emails.ocorrencia');
    }
}
