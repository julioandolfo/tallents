<?php

namespace App\Models;

use App\Models\Concerns\EscopoPorColaborador;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ocorrencia extends Model
{
    use EscopoPorColaborador;
    use HasFactory;

    protected $fillable = [
        'empresa_id',
        'colaborador_id',
        'tipo_ocorrencia_id',
        'registrado_por',
        'titulo',
        'gravidade',
        'descricao',
        'data_ocorrencia',
        'hora_ocorrencia',
        'tempo_atraso_minutos',
        'tipo_ponto',
        'notificar_colaborador',
    ];

    protected $casts = [
        'data_ocorrencia'       => 'date',
        'tempo_atraso_minutos'  => 'integer',
        'notificar_colaborador' => 'boolean',
    ];

    public const TIPOS_PONTO = [
        'ENTRADA'   => 'Entrada',
        'SAIDA'     => 'Saída',
        'INTERVALO' => 'Intervalo',
    ];

    // Aliases usados pelas views (form usa "data"/"observacao").
    public function getDataAttribute()
    {
        return $this->data_ocorrencia;
    }

    public function getObservacaoAttribute(): ?string
    {
        return $this->descricao;
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function colaborador(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class);
    }

    public function tipoOcorrencia(): BelongsTo
    {
        return $this->belongsTo(TipoOcorrencia::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'registrado_por');
    }

    public function anexos(): HasMany
    {
        return $this->hasMany(OcorrenciaAnexo::class)->latest();
    }

    public function comentarios(): HasMany
    {
        return $this->hasMany(OcorrenciaComentario::class)->oldest();
    }

    public function historico(): HasMany
    {
        return $this->hasMany(OcorrenciaHistorico::class)->latest();
    }

    /** Registra um evento no histórico da ocorrência. */
    public function registrarHistorico(string $acao, ?string $detalhe = null): void
    {
        $this->historico()->create([
            'usuario_id' => auth()->id(),
            'acao'       => $acao,
            'detalhe'    => $detalhe,
        ]);
    }
}
