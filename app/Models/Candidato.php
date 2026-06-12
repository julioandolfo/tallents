<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Candidato extends Model
{
    protected $table = 'candidatos';

    protected $fillable = [
        'vaga_id', 'nome', 'email', 'telefone', 'linkedin',
        'curriculo', 'etapa', 'nota', 'observacoes',
    ];

    public const ETAPAS = [
        'TRIAGEM'    => 'Triagem',
        'ENTREVISTA' => 'Entrevista',
        'TESTE'      => 'Teste',
        'PROPOSTA'   => 'Proposta',
        'CONTRATADO' => 'Contratado',
        'REPROVADO'  => 'Reprovado',
    ];

    /** Etapas que compõem o funil (exclui o estado final de reprovação). */
    public const ETAPAS_FUNIL = ['TRIAGEM', 'ENTREVISTA', 'TESTE', 'PROPOSTA', 'CONTRATADO'];

    public const CORES = [
        'TRIAGEM'    => 'gray',
        'ENTREVISTA' => 'blue',
        'TESTE'      => 'purple',
        'PROPOSTA'   => 'yellow',
        'CONTRATADO' => 'green',
        'REPROVADO'  => 'red',
    ];

    public function etapaLabel(): string
    {
        return self::ETAPAS[$this->etapa] ?? $this->etapa;
    }

    public function cor(): string
    {
        return self::CORES[$this->etapa] ?? 'gray';
    }

    public function curriculoUrl(): ?string
    {
        return $this->curriculo ? Storage::url($this->curriculo) : null;
    }

    public function vaga(): BelongsTo
    {
        return $this->belongsTo(Vaga::class);
    }
}
