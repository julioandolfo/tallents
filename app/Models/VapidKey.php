<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VapidKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'public_key',
        'private_key',
    ];

    protected $hidden = [
        'private_key',
    ];
}
