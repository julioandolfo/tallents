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
        'categoria',
        'permite_tempo_atraso',
        'permite_tipo_ponto',
        'gravidade',
        'ativo',
    ];

    protected $casts = [
        'ativo'                => 'boolean',
        'permite_tempo_atraso' => 'boolean',
        'permite_tipo_ponto'   => 'boolean',
    ];

    public const CATEGORIAS = [
        'ATRASO' => 'Atraso',
        'FALTA'  => 'Falta',
        'PONTO'  => 'Ponto',
        'OUTROS' => 'Outros',
    ];

    public function categoriaLabel(): string
    {
        return self::CATEGORIAS[$this->categoria] ?? ($this->categoria ?: '—');
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function ocorrencias(): HasMany
    {
        return $this->hasMany(Ocorrencia::class);
    }
}
