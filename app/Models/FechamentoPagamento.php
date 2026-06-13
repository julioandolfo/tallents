<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FechamentoPagamento extends Model
{
    use HasFactory;

    protected $table = 'fechamentos_pagamento';

    protected $fillable = [
        'empresa_id',
        'criado_por',
        'mes',
        'ano',
        'status',
        'total_salarios',
        'total_horas_extras',
        'total_bonus',
        'total_geral',
        'observacoes',
        'fechado_por',
        'fechado_em',
    ];

    protected $casts = [
        'total_salarios'    => 'decimal:2',
        'total_horas_extras' => 'decimal:2',
        'total_bonus'       => 'decimal:2',
        'total_geral'       => 'decimal:2',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function criadoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'criado_por');
    }

    public function itens(): HasMany
    {
        return $this->hasMany(FechamentoPagamentoItem::class, 'fechamento_id');
    }
}
