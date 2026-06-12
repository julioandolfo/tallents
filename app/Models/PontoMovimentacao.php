<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PontoMovimentacao extends Model
{
    protected $table = 'pontos_movimentacoes';

    protected $fillable = [
        'colaborador_id', 'registrado_por', 'origem', 'pontos', 'descricao',
    ];

    protected $casts = [
        'pontos' => 'integer',
    ];

    public const ORIGENS = [
        'MANUAL'    => 'Lançamento manual',
        'ELOGIO'    => 'Elogio',
        'AVALIACAO' => 'Avaliação',
        'META'      => 'Meta atingida',
        'RESGATE'   => 'Resgate na loja',
        'AJUSTE'    => 'Ajuste',
    ];

    public function origemLabel(): string
    {
        return self::ORIGENS[$this->origem] ?? $this->origem;
    }

    public function colaborador(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'registrado_por');
    }
}
