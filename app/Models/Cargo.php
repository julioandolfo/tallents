<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cargo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'empresa_id',
        'nome',
        'descricao',
        'nivel_hierarquico_id',
        'salario_base',
        'salario_maximo',
        'ativo',
    ];

    protected $casts = [
        'salario_base'   => 'decimal:2',
        'salario_maximo' => 'decimal:2',
        'ativo'          => 'boolean',
    ];

    public function nivelHierarquico(): BelongsTo
    {
        return $this->belongsTo(NivelHierarquico::class);
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function colaboradores(): HasMany
    {
        return $this->hasMany(Colaborador::class);
    }
}
