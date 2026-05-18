<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Colaborador extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'empresa_id',
        'setor_id',
        'cargo_id',
        'nivel_hierarquico_id',
        'lider_id',
        'nome',
        'cpf',
        'rg',
        'data_nascimento',
        'email',
        'telefone',
        'celular',
        'tipo_contrato',
        'data_admissao',
        'data_demissao',
        'salario',
        'status',
        'foto',
        'senha_hash',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'observacoes',
    ];

    protected $hidden = [
        'senha_hash',
    ];

    protected $casts = [
        'salario'         => 'decimal:2',
        'data_nascimento' => 'date',
        'data_admissao'   => 'date',
        'data_demissao'   => 'date',
        'status'          => 'string',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function setor(): BelongsTo
    {
        return $this->belongsTo(Setor::class);
    }

    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class);
    }

    public function nivelHierarquico(): BelongsTo
    {
        return $this->belongsTo(NivelHierarquico::class);
    }

    public function lider(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class, 'lider_id');
    }

    public function subordinados(): HasMany
    {
        return $this->hasMany(Colaborador::class, 'lider_id');
    }

    public function ocorrencias(): HasMany
    {
        return $this->hasMany(Ocorrencia::class);
    }

    public function horasExtras(): HasMany
    {
        return $this->hasMany(HoraExtra::class);
    }

    public function promocoes(): HasMany
    {
        return $this->hasMany(Promocao::class);
    }

    public function bonus(): HasMany
    {
        return $this->hasMany(ColaboradorBonus::class);
    }
}
