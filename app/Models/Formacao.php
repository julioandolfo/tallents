<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Formacao extends Model
{
    protected $table = 'formacoes';

    protected $fillable = [
        'colaborador_id', 'nivel', 'curso', 'instituicao', 'situacao', 'ano_conclusao',
    ];

    public const NIVEIS = [
        'FUNDAMENTAL' => 'Ensino Fundamental',
        'MEDIO'       => 'Ensino Médio',
        'TECNICO'     => 'Técnico',
        'GRADUACAO'   => 'Graduação',
        'POS'         => 'Pós-graduação',
        'MESTRADO'    => 'Mestrado',
        'DOUTORADO'   => 'Doutorado',
        'CURSO'       => 'Curso livre',
    ];

    public const SITUACOES = [
        'CURSANDO'  => 'Cursando',
        'CONCLUIDO' => 'Concluído',
        'TRANCADO'  => 'Trancado',
    ];

    public function nivelLabel(): string
    {
        return self::NIVEIS[$this->nivel] ?? $this->nivel;
    }

    public function situacaoLabel(): string
    {
        return self::SITUACOES[$this->situacao] ?? $this->situacao;
    }

    public function colaborador(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class);
    }
}
