<?php

namespace App\Mail;

use App\Models\Ocorrencia;

class OcorrenciaRegistrada extends TemplateMailable
{
    public function __construct(public Ocorrencia $ocorrencia) {}

    public function build()
    {
        return $this->comTemplate('ocorrencia', [
            'nome' => optional($this->ocorrencia->colaborador)->nome,
            'tipo' => optional($this->ocorrencia->tipoOcorrencia)->nome ?? '—',
            'data' => optional($this->ocorrencia->data_ocorrencia)->format('d/m/Y'),
        ], 'emails.ocorrencia', 'Registro de ocorrência');
    }
}
