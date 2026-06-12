<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tarefa extends Model
{
    protected $table = 'tarefas';

    protected $fillable = [
        'quadro_id', 'responsavel_id', 'titulo', 'descricao', 'status', 'prioridade', 'prazo', 'ordem',
    ];

    protected $casts = [
        'prazo' => 'date',
    ];

    public const STATUS = [
        'A_FAZER'   => 'A fazer',
        'FAZENDO'   => 'Fazendo',
        'CONCLUIDO' => 'Concluído',
    ];

    public const PRIORIDADES = [
        'BAIXA' => 'Baixa',
        'MEDIA' => 'Média',
        'ALTA'  => 'Alta',
    ];

    public const CORES_PRIORIDADE = [
        'BAIXA' => 'gray',
        'MEDIA' => 'blue',
        'ALTA'  => 'red',
    ];

    public function statusLabel(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function prioridadeLabel(): string
    {
        return self::PRIORIDADES[$this->prioridade] ?? $this->prioridade;
    }

    public function corPrioridade(): string
    {
        return self::CORES_PRIORIDADE[$this->prioridade] ?? 'gray';
    }

    public function quadro(): BelongsTo
    {
        return $this->belongsTo(Quadro::class);
    }

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'responsavel_id');
    }
}
