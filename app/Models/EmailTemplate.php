<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'slug',
        'assunto',
        'corpo',
        'variaveis',
        'ativo',
    ];

    protected $casts = [
        'variaveis' => 'array',
        'ativo'     => 'boolean',
    ];
}
