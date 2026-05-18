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
        'descricao',
        'data_ocorrencia',
    ];

    protected $casts = [
        'data_ocorrencia' => 'date',
    ];

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
