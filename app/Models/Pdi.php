<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pdi extends Model
{
    protected $table = 'pdis';

    protected $fillable = [
        'colaborador_id', 'responsavel_id', 'titulo', 'descricao', 'status', 'prazo',
    ];

    protected $casts = [
        'prazo' => 'date',
    ];

    public const STATUS = [
        'EM_ANDAMENTO' => 'Em andamento',
        'CONCLUIDO'    => 'Concluído',
        'CANCELADO'    => 'Cancelado',
    ];

    public function statusLabel(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function statusCor(): string
    {
        return ['EM_ANDAMENTO' => 'blue', 'CONCLUIDO' => 'green', 'CANCELADO' => 'gray'][$this->status] ?? 'gray';
    }

    public function colaborador(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class);
    }

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'responsavel_id');
    }

    public function acoes(): HasMany
    {
        return $this->hasMany(PdiAcao::class)->orderBy('id');
    }

    public function progresso(): int
    {
        $total = $this->acoes()->count();
        if ($total === 0) {
            return 0;
        }

        return (int) round($this->acoes()->where('concluida', true)->count() / $total * 100);
    }
}
