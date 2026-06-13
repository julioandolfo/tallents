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
        'metodo_assinatura', 'autentique_document_id', 'autentique_signature_url',
        'enviado_assinatura_em',
    ];

    protected $casts = [
        'assinado_em'           => 'datetime',
        'enviado_assinatura_em' => 'datetime',
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

    public const METODOS = [
        'INTERNO'    => 'Assinatura no portal (aceite + IP)',
        'AUTENTIQUE' => 'Assinatura eletrônica (Autentique)',
    ];

    /** Variáveis disponíveis para uso no conteúdo do contrato. */
    public const VARIAVEIS = 'nome, cpf, rg, cargo, setor, empresa, salario, data_admissao, data_hoje';

    public function metodoLabel(): string
    {
        return self::METODOS[$this->metodo_assinatura] ?? ($this->metodo_assinatura ?? 'INTERNO');
    }

    /** Mapa de variáveis -> valores, a partir do colaborador vinculado. */
    public function dadosVariaveis(): array
    {
        $c = $this->colaborador;

        return [
            'nome'          => $c->nome ?? '',
            'cpf'           => $c->cpf ?? '',
            'rg'            => $c->rg ?? '',
            'cargo'         => optional($c?->cargo)->nome ?? '',
            'setor'         => optional($c?->setor)->nome ?? '',
            'empresa'       => optional($c?->empresa)->nome ?? '',
            'salario'       => isset($c->salario) ? 'R$ ' . number_format($c->salario, 2, ',', '.') : '',
            'data_admissao' => optional($c?->data_admissao)->format('d/m/Y') ?? '',
            'data_hoje'     => now()->format('d/m/Y'),
        ];
    }

    /** Conteúdo com as variáveis {{ x }} substituídas pelos dados do colaborador. */
    public function conteudoRenderizado(): string
    {
        $dados = $this->dadosVariaveis();

        return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', function ($m) use ($dados) {
            return array_key_exists($m[1], $dados) ? (string) $dados[$m[1]] : $m[0];
        }, (string) $this->conteudo);
    }

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
