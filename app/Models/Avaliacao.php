<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Avaliacao extends Model
{
    protected $table = 'avaliacoes';

    protected $fillable = [
        'colaborador_id', 'avaliador_id', 'ciclo', 'tipo', 'nota_geral',
        'pontos_fortes', 'pontos_melhorar', 'status', 'data',
    ];

    protected $casts = [
        'nota_geral' => 'decimal:2',
        'data'       => 'date',
    ];

    public const TIPOS = [
        'AUTO'   => 'Autoavaliação',
        'GESTOR' => 'Avaliação do gestor',
        '360'    => 'Avaliação 360°',
    ];

    public const STATUS = [
        'PENDENTE'  => 'Pendente',
        'CONCLUIDA' => 'Concluída',
    ];

    public function tipoLabel(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }

    public function statusLabel(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function colaborador(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class);
    }

    public function avaliador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'avaliador_id');
    }
}
