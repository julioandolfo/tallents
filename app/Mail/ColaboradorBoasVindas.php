<?php

namespace App\Mail;

use App\Models\Colaborador;

class ColaboradorBoasVindas extends TemplateMailable
{
    public function __construct(public Colaborador $colaborador, public ?string $emailLogin = null) {}

    public function build()
    {
        $empresa = optional($this->colaborador->empresa)->nome ?? config('app.name');

        return $this->comTemplate('boas_vindas', [
            'nome'        => $this->colaborador->nome,
            'empresa'     => $empresa,
            'cargo'       => optional($this->colaborador->cargo)->nome ?? '—',
            'setor'       => optional($this->colaborador->setor)->nome ?? '—',
            'email_login' => $this->emailLogin ?? '',
        ], 'emails.boas-vindas', 'Bem-vindo(a) à ' . $empresa);
    }
}
