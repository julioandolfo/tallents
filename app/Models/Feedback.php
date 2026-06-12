<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    protected $table = 'feedbacks';

    protected $fillable = [
        'colaborador_id', 'autor_id', 'tipo', 'mensagem', 'visivel_colaborador', 'data',
    ];

    protected $casts = [
        'visivel_colaborador' => 'boolean',
        'data'                => 'date',
    ];

    public const TIPOS = [
        'ELOGIO'      => 'Elogio',
        'CONSTRUTIVO' => 'Construtivo',
        'GERAL'       => 'Geral',
    ];

    public const CORES = [
        'ELOGIO'      => 'green',
        'CONSTRUTIVO' => 'yellow',
        'GERAL'       => 'gray',
    ];

    public function tipoLabel(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }

    public function cor(): string
    {
        return self::CORES[$this->tipo] ?? 'gray';
    }

    public function colaborador(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class);
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'autor_id');
    }
}
