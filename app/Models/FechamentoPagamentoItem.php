<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FechamentoPagamentoItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'fechamento_id',
        'colaborador_id',
        'salario_base',
        'total_horas_extras',
        'total_bonus',
        'total',
    ];

    protected $casts = [
        'salario_base'      => 'decimal:2',
        'total_horas_extras' => 'decimal:2',
        'total_bonus'       => 'decimal:2',
        'total'             => 'decimal:2',
    ];

    public function fechamento(): BelongsTo
    {
        return $this->belongsTo(FechamentoPagamento::class, 'fechamento_id');
    }

    public function colaborador(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class);
    }

    public function bonus(): HasMany
    {
        return $this->hasMany(FechamentoPagamentoBonus::class, 'fechamento_item_id');
    }
}
