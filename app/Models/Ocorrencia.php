<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ocorrencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id',
        'colaborador_id',
        'tipo_ocorrencia_id',
        'registrado_por',
        'titulo',
        'gravidade',
        'descricao',
        'data_ocorrencia',
        'notificar_colaborador',
    ];

    protected $casts = [
        'data_ocorrencia'       => 'date',
        'notificar_colaborador' => 'boolean',
    ];

    // Aliases usados pelas views (form usa "data"/"observacao").
    public function getDataAttribute()
    {
        return $this->data_ocorrencia;
    }

    public function getObservacaoAttribute(): ?string
    {
        return $this->descricao;
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function colaborador(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class);
    }

    public function tipoOcorrencia(): BelongsTo
    {
        return $this->belongsTo(TipoOcorrencia::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'registrado_por');
    }
}
