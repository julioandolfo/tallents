<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BancoHorasMovimentacao extends Model
{
    protected $table = 'banco_horas_movimentacoes';

    protected $fillable = [
        'colaborador_id', 'empresa_id', 'registrado_por', 'tipo', 'horas', 'motivo', 'data',
    ];

    protected $casts = [
        'horas' => 'decimal:2',
        'data'  => 'date',
    ];

    public function colaborador(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'registrado_por');
    }
}
