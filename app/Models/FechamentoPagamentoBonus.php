<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FechamentoPagamentoBonus extends Model
{
    use HasFactory;

    protected $fillable = [
        'fechamento_item_id',
        'colaborador_bonus_id',
        'valor',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
    ];

    public function fechamentoItem(): BelongsTo
    {
        return $this->belongsTo(FechamentoPagamentoItem::class, 'fechamento_item_id');
    }

    public function colaboradorBonus(): BelongsTo
    {
        return $this->belongsTo(ColaboradorBonus::class);
    }
}
