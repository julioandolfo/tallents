<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Empresa extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nome',
        'cnpj',
        'endereco',
        'cidade',
        'estado',
        'telefone',
        'email',
        'percentual_hora_extra',
        'logo',
        'ativa',
    ];

    protected $casts = [
        'ativa'                 => 'boolean',
        'percentual_hora_extra' => 'decimal:2',
    ];

    public function setores(): HasMany
    {
        return $this->hasMany(Setor::class);
    }

    public function cargos(): HasMany
    {
        return $this->hasMany(Cargo::class);
    }

    public function colaboradores(): HasMany
    {
        return $this->hasMany(Colaborador::class);
    }

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'usuarios_empresas', 'empresa_id', 'user_id');
    }

    public function niveis_hierarquicos(): HasMany
    {
        return $this->hasMany(NivelHierarquico::class);
    }

    public function tipos_ocorrencias(): HasMany
    {
        return $this->hasMany(TipoOcorrencia::class);
    }

    public function tipos_bonus(): HasMany
    {
        return $this->hasMany(TipoBonus::class);
    }

    public function fechamentos(): HasMany
    {
        return $this->hasMany(FechamentoPagamento::class);
    }
}
