<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Contrato extends Model
{
    protected $table = 'contratos';

    protected $fillable = [
        'colaborador_id', 'criado_por', 'titulo', 'tipo', 'conteudo',
        'arquivo', 'status', 'assinado_em', 'assinatura_ip',
    ];

    protected $casts = [
        'assinado_em' => 'datetime',
    ];

    public const TIPOS = [
        'CONTRATO' => 'Contrato de trabalho',
        'ESTAGIO'  => 'Termo de estágio',
        'ADITIVO'  => 'Aditivo contratual',
        'TERMO'    => 'Termo / declaração',
    ];

    public const STATUS = [
        'PENDENTE'  => 'Pendente',
        'ASSINADO'  => 'Assinado',
        'CANCELADO' => 'Cancelado',
    ];

    public function tipoLabel(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }

    public function statusLabel(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function cor(): string
    {
        return ['PENDENTE' => 'yellow', 'ASSINADO' => 'green', 'CANCELADO' => 'red'][$this->status] ?? 'gray';
    }

    public function arquivoUrl(): ?string
    {
        return $this->arquivo ? Storage::url($this->arquivo) : null;
    }

    public function colaborador(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class);
    }

    public function criadoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'criado_por');
    }
}
