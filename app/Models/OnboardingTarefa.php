<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingTarefa extends Model
{
    protected $table = 'onboarding_tarefas';

    protected $fillable = [
        'onboarding_id', 'titulo', 'descricao', 'concluida', 'concluida_em', 'ordem',
    ];

    protected $casts = [
        'concluida'    => 'boolean',
        'concluida_em' => 'datetime',
    ];

    public function onboarding(): BelongsTo
    {
        return $this->belongsTo(Onboarding::class);
    }
}
