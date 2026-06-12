<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resgate extends Model
{
    protected $table = 'resgates';

    protected $fillable = [
        'colaborador_id', 'recompensa_id', 'custo_pontos', 'status',
    ];

    protected $casts = [
        'custo_pontos' => 'integer',
    ];

    public const STATUS = [
        'PENDENTE'  => 'Pendente',
        'APROVADO'  => 'Aprovado',
        'ENTREGUE'  => 'Entregue',
        'CANCELADO' => 'Cancelado',
    ];

    public const CORES = [
        'PENDENTE'  => 'yellow',
        'APROVADO'  => 'blue',
        'ENTREGUE'  => 'green',
        'CANCELADO' => 'red',
    ];

    public function statusLabel(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function cor(): string
    {
        return self::CORES[$this->status] ?? 'gray';
    }

    public function colaborador(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class);
    }

    public function recompensa(): BelongsTo
    {
        return $this->belongsTo(Recompensa::class);
    }
}
