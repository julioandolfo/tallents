<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Documento extends Model
{
    protected $table = 'documentos';

    protected $fillable = [
        'empresa_id', 'titulo', 'descricao', 'categoria', 'arquivo', 'url',
    ];

    public const CATEGORIAS = [
        'MANUAL'     => 'Manual de conduta',
        'POLITICA'   => 'Política interna',
        'FORMULARIO' => 'Formulário',
        'OUTRO'      => 'Outro',
    ];

    public function categoriaLabel(): string
    {
        return self::CATEGORIAS[$this->categoria] ?? $this->categoria;
    }

    /** Link de download/abertura: arquivo no storage ou URL externa. */
    public function link(): ?string
    {
        if ($this->arquivo) {
            return Storage::url($this->arquivo);
        }

        return $this->url;
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}
