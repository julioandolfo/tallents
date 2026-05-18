<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OnesignalConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_id',
        'rest_api_key',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    protected $hidden = [
        'rest_api_key',
    ];
}
