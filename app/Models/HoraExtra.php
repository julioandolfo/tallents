<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HoraExtra extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id',
        'colaborador_id',
        'registrado_por',
        'data',
        'horas',
        'percentual',
        'valor',
        'observacao',
        'aprovado',
    ];

    protected $casts = [
        'horas'      => 'decimal:2',
        'percentual' => 'decimal:2',
        'valor'      => 'decimal:2',
        'data'       => 'date',
        'aprovado'   => 'boolean',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function colaborador(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'registrado_por');
    }
}
