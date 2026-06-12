<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dependente extends Model
{
    protected $table = 'dependentes';

    protected $fillable = [
        'colaborador_id', 'nome', 'parentesco', 'data_nascimento',
        'cpf', 'dependente_ir', 'observacoes',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'dependente_ir'   => 'boolean',
    ];

    public const PARENTESCOS = [
        'FILHO'   => 'Filho(a)',
        'CONJUGE' => 'Cônjuge',
        'PAI'     => 'Pai',
        'MAE'     => 'Mãe',
        'OUTRO'   => 'Outro',
    ];

    public function parentescoLabel(): string
    {
        return self::PARENTESCOS[$this->parentesco] ?? $this->parentesco;
    }

    public function colaborador(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class);
    }
}
