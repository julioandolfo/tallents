<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OcorrenciaHistorico extends Model
{
    protected $table = 'ocorrencias_historico';

    protected $fillable = ['ocorrencia_id', 'usuario_id', 'acao', 'detalhe'];

    public function ocorrencia(): BelongsTo
    {
        return $this->belongsTo(Ocorrencia::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
