<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vaga extends Model
{
    protected $table = 'vagas';

    protected $fillable = [
        'empresa_id', 'cargo_id', 'responsavel_id', 'titulo', 'descricao',
        'requisitos', 'modalidade', 'tipo_contrato', 'salario_min', 'salario_max',
        'quantidade', 'status', 'aberta_em',
    ];

    protected $casts = [
        'salario_min' => 'decimal:2',
        'salario_max' => 'decimal:2',
        'aberta_em'   => 'date',
    ];

    public const STATUS = [
        'ABERTA'  => 'Aberta',
        'PAUSADA' => 'Pausada',
        'FECHADA' => 'Fechada',
    ];

    public const MODALIDADES = [
        'PRESENCIAL' => 'Presencial',
        'REMOTO'     => 'Remoto',
        'HIBRIDO'    => 'Híbrido',
    ];

    public function statusLabel(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function modalidadeLabel(): string
    {
        return self::MODALIDADES[$this->modalidade] ?? $this->modalidade;
    }

    public function statusCor(): string
    {
        return ['ABERTA' => 'green', 'PAUSADA' => 'yellow', 'FECHADA' => 'gray'][$this->status] ?? 'gray';
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class);
    }

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'responsavel_id');
    }

    public function candidatos(): HasMany
    {
        return $this->hasMany(Candidato::class)->orderByDesc('nota')->orderBy('nome');
    }
}
