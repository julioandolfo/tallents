<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Advertencia extends Model
{
    protected $table = 'advertencias';

    protected $fillable = [
        'empresa_id', 'colaborador_id', 'ocorrencia_id', 'aplicada_por',
        'tipo', 'motivo', 'data', 'dias_suspensao',
    ];

    protected $casts = [
        'data' => 'date',
    ];

    public const TIPOS = [
        'VERBAL'    => 'Advertência verbal',
        'ESCRITA'   => 'Advertência escrita',
        'SUSPENSAO' => 'Suspensão',
    ];

    public function tipoLabel(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function colaborador(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class);
    }

    public function ocorrencia(): BelongsTo
    {
        return $this->belongsTo(Ocorrencia::class);
    }

    public function aplicadaPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'aplicada_por');
    }
}
