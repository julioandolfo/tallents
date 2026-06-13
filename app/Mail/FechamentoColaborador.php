<?php

namespace App\Mail;

use App\Models\FechamentoPagamentoItem;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FechamentoColaborador extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public FechamentoPagamentoItem $item) {}

    public function build()
    {
        return $this->subject('Seu demonstrativo de pagamento')->view('emails.fechamento');
    }
}
