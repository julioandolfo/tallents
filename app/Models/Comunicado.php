<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comunicado extends Model
{
    protected $table = 'comunicados';

    protected $fillable = [
        'empresa_id', 'autor_id', 'titulo', 'conteudo',
        'categoria', 'destaque', 'publicado', 'publicado_em',
    ];

    protected $casts = [
        'destaque'     => 'boolean',
        'publicado'    => 'boolean',
        'publicado_em' => 'datetime',
    ];

    public const CATEGORIAS = [
        'GERAL'   => 'Geral',
        'RH'      => 'Recursos Humanos',
        'EVENTO'  => 'Evento',
        'URGENTE' => 'Urgente',
    ];

    public const CORES = [
        'GERAL'   => 'gray',
        'RH'      => 'blue',
        'EVENTO'  => 'purple',
        'URGENTE' => 'red',
    ];

    public function categoriaLabel(): string
    {
        return self::CATEGORIAS[$this->categoria] ?? $this->categoria;
    }

    public function cor(): string
    {
        return self::CORES[$this->categoria] ?? 'gray';
    }

    public function scopePublicados($query)
    {
        return $query->where('publicado', true);
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'autor_id');
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}
