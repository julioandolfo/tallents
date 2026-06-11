<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Demissao extends Model
{
    protected $table = 'demissoes';

    protected $fillable = [
        'colaborador_id', 'empresa_id', 'registrado_por',
        'tipo', 'data_desligamento', 'aviso_previo', 'motivo',
    ];

    protected $casts = [
        'data_desligamento' => 'date',
    ];

    public const TIPOS = [
        'PEDIDO'          => 'Pedido de demissão',
        'SEM_JUSTA_CAUSA' => 'Sem justa causa',
        'JUSTA_CAUSA'     => 'Justa causa',
        'FIM_CONTRATO'    => 'Fim de contrato',
        'ACORDO'          => 'Acordo',
    ];

    public const AVISOS = [
        'TRABALHADO' => 'Trabalhado',
        'INDENIZADO' => 'Indenizado',
        'DISPENSADO' => 'Dispensado',
    ];

    public function tipoLabel(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }

    public function avisoLabel(): ?string
    {
        return $this->aviso_previo ? (self::AVISOS[$this->aviso_previo] ?? $this->aviso_previo) : null;
    }

    public function colaborador(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class);
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'registrado_por');
    }
}
