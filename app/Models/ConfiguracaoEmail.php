<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConfiguracaoEmail extends Model
{
    use HasFactory;

    protected $table = 'configuracoes_email';

    protected $fillable = [
        'driver',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'from_email',
        'from_name',
    ];

    protected $hidden = [
        'smtp_password',
    ];
}
