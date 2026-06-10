<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ColaboradorBonus extends Model
{
    use HasFactory;

    protected $table = 'colaboradores_bonus';

    protected $fillable = [
        'colaborador_id',
        'tipo_bonus_id',
        'valor',
        'ativo',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'ativo' => 'boolean',
    ];

    public function colaborador(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class);
    }

    public function tipoBonus(): BelongsTo
    {
        return $this->belongsTo(TipoBonus::class);
    }
}
