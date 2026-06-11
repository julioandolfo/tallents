<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Onboarding extends Model
{
    protected $table = 'onboardings';

    protected $fillable = [
        'colaborador_id', 'empresa_id', 'responsavel_id',
        'data_inicio', 'status', 'observacoes',
    ];

    protected $casts = [
        'data_inicio' => 'date',
    ];

    public const STATUS = [
        'EM_ANDAMENTO' => 'Em andamento',
        'CONCLUIDO'    => 'Concluído',
        'CANCELADO'    => 'Cancelado',
    ];

    /** Checklist padrão criado para um novo onboarding. */
    public const TAREFAS_PADRAO = [
        'Enviar documentos admissionais',
        'Assinar contrato de trabalho',
        'Cadastrar no sistema de ponto',
        'Configurar e-mail corporativo',
        'Entregar equipamentos (notebook, crachá)',
        'Apresentar a equipe e o gestor direto',
        'Treinamento sobre cultura e código de conduta',
        'Configurar acessos aos sistemas internos',
    ];

    public function statusLabel(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function colaborador(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class);
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'responsavel_id');
    }

    public function tarefas(): HasMany
    {
        return $this->hasMany(OnboardingTarefa::class)->orderBy('ordem')->orderBy('id');
    }

    /** Percentual de conclusão do checklist (0-100). */
    public function progresso(): int
    {
        $total = $this->tarefas()->count();

        if ($total === 0) {
            return 0;
        }

        $feitas = $this->tarefas()->where('concluida', true)->count();

        return (int) round(($feitas / $total) * 100);
    }
}
