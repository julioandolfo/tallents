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
        'data_inicio',
        'data_fim',
        'observacoes',
        'ativo',
    ];

    protected $casts = [
        'valor'       => 'decimal:2',
        'data_inicio' => 'date',
        'data_fim'    => 'date',
        'ativo'       => 'boolean',
    ];

    /**
     * Bônus vigentes num período (mês/ano): ativo e cuja janela
     * [data_inicio, data_fim] intercepta o mês de referência.
     * Datas nulas = sempre vigente.
     */
    public function scopeVigentesEm($query, int $mes, int $ano)
    {
        $inicioMes = \Carbon\Carbon::create($ano, $mes, 1)->toDateString();
        $fimMes    = \Carbon\Carbon::create($ano, $mes, 1)->endOfMonth()->toDateString();

        return $query->where('ativo', true)
            ->where(fn($q) => $q->whereNull('data_inicio')->orWhere('data_inicio', '<=', $fimMes))
            ->where(fn($q) => $q->whereNull('data_fim')->orWhere('data_fim', '>=', $inicioMes));
    }

    public function colaborador(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class);
    }

    public function tipoBonus(): BelongsTo
    {
        return $this->belongsTo(TipoBonus::class);
    }
}
