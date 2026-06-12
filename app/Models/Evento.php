<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evento extends Model
{
    protected $table = 'eventos';

    protected $fillable = [
        'empresa_id', 'titulo', 'descricao', 'tipo', 'inicio', 'fim', 'local',
    ];

    protected $casts = [
        'inicio' => 'datetime',
        'fim'    => 'datetime',
    ];

    public const TIPOS = [
        'REUNIAO'     => 'Reunião',
        'TREINAMENTO' => 'Treinamento',
        'FOLGA'       => 'Folga / Feriado',
        'ANIVERSARIO' => 'Aniversário',
        'OUTRO'       => 'Outro',
    ];

    public const CORES = [
        'REUNIAO'     => 'blue',
        'TREINAMENTO' => 'purple',
        'FOLGA'       => 'green',
        'ANIVERSARIO' => 'yellow',
        'OUTRO'       => 'gray',
    ];

    public function tipoLabel(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }

    public function cor(): string
    {
        return self::CORES[$this->tipo] ?? 'gray';
    }

    public function scopeProximos($query)
    {
        return $query->where('inicio', '>=', now()->startOfDay())->orderBy('inicio');
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}
