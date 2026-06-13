<?php

namespace App\Models;

use App\Models\Concerns\EscopoPorColaborador;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promocao extends Model
{
    use EscopoPorColaborador;
    use HasFactory;

    protected $table = 'promocoes';

    protected $fillable = [
        'empresa_id',
        'colaborador_id',
        'registrado_por',
        'tipo',
        'cargo_anterior_id',
        'cargo_novo_id',
        'salario_anterior',
        'salario_novo',
        'data_promocao',
        'motivo',
    ];

    protected $casts = [
        'salario_anterior' => 'decimal:2',
        'salario_novo'     => 'decimal:2',
        'data_promocao'    => 'date',
    ];

    // Alias usado pelas views (form usa "data").
    public function getDataAttribute()
    {
        return $this->data_promocao;
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function colaborador(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class);
    }

    public function cargoAnterior(): BelongsTo
    {
        return $this->belongsTo(Cargo::class, 'cargo_anterior_id');
    }

    public function cargoNovo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class, 'cargo_novo_id');
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'registrado_por');
    }
}
