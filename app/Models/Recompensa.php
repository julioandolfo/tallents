<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Recompensa extends Model
{
    protected $table = 'recompensas';

    protected $fillable = [
        'nome', 'descricao', 'custo_pontos', 'estoque', 'imagem', 'ativa',
    ];

    protected $casts = [
        'custo_pontos' => 'integer',
        'estoque'      => 'integer',
        'ativa'        => 'boolean',
    ];

    public function imagemUrl(): ?string
    {
        return $this->imagem ? Storage::url($this->imagem) : null;
    }

    public function disponivel(): bool
    {
        return $this->ativa && (is_null($this->estoque) || $this->estoque > 0);
    }

    public function resgates(): HasMany
    {
        return $this->hasMany(Resgate::class);
    }
}
