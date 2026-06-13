<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracaoPush extends Model
{
    protected $table = 'configuracoes_push';

    protected $fillable = ['provider', 'onesignal_app_id', 'onesignal_api_key', 'ativo'];

    protected $casts = ['ativo' => 'boolean'];
}
