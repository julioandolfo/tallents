<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quadro extends Model
{
    protected $table = 'quadros';

    protected $fillable = [
        'criado_por', 'nome', 'descricao',
    ];

    public function criadoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'criado_por');
    }

    public function tarefas(): HasMany
    {
        return $this->hasMany(Tarefa::class)->orderBy('ordem')->orderBy('id');
    }
}
