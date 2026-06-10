<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoOcorrencia extends Model
{
    use HasFactory;

    protected $table = 'tipos_ocorrencias';

    protected $fillable = [
        'empresa_id',
        'nome',
        'descricao',
        'tipo',
        'gravidade',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function ocorrencias(): HasMany
    {
        return $this->hasMany(Ocorrencia::class);
    }
}
