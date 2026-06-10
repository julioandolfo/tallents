<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoBonus extends Model
{
    use HasFactory;

    protected $table = 'tipos_bonus';

    protected $fillable = [
        'empresa_id',
        'nome',
        'descricao',
        'tipo_calculo',
        'percentual',
        'fixo',
        'ativo',
    ];

    protected $casts = [
        'percentual' => 'decimal:2',
        'fixo'       => 'decimal:2',
        'ativo'      => 'boolean',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function colaboradorBonus(): HasMany
    {
        return $this->hasMany(ColaboradorBonus::class);
    }
}
