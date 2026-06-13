<?php

namespace App\Mail;

use App\Models\Promocao;

class PromocaoRegistrada extends TemplateMailable
{
    public function __construct(public Promocao $promocao) {}

    public function build()
    {
        return $this->comTemplate('promocao', [
            'nome'         => optional($this->promocao->colaborador)->nome,
            'cargo_novo'   => optional($this->promocao->cargoNovo)->nome ?? '—',
            'salario_novo' => $this->promocao->salario_novo ? 'R$ ' . number_format($this->promocao->salario_novo, 2, ',', '.') : '—',
            'data'         => optional($this->promocao->data_promocao)->format('d/m/Y'),
        ], 'emails.promocao', 'Você foi promovido(a)! 🎉');
    }
}
