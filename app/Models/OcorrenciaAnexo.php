<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OcorrenciaAnexo extends Model
{
    protected $table = 'ocorrencias_anexos';

    protected $fillable = ['ocorrencia_id', 'usuario_id', 'nome_original', 'caminho', 'mime', 'tamanho'];

    public function ocorrencia(): BelongsTo
    {
        return $this->belongsTo(Ocorrencia::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
