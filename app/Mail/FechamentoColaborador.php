<?php

namespace App\Mail;

use App\Models\FechamentoPagamentoItem;

class FechamentoColaborador extends TemplateMailable
{
    public function __construct(public FechamentoPagamentoItem $item) {}

    public function build()
    {
        return $this->comTemplate('fechamento', [
            'nome'  => optional($this->item->colaborador)->nome,
            'mes'   => str_pad((string) optional($this->item->fechamento)->mes, 2, '0', STR_PAD_LEFT),
            'ano'   => optional($this->item->fechamento)->ano,
            'total' => 'R$ ' . number_format((float) $this->item->total, 2, ',', '.'),
        ], 'emails.fechamento', 'Seu demonstrativo de pagamento');
    }
}
