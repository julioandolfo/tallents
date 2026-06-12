<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PdiAcao extends Model
{
    protected $table = 'pdi_acoes';

    protected $fillable = [
        'pdi_id', 'descricao', 'concluida', 'prazo',
    ];

    protected $casts = [
        'concluida' => 'boolean',
        'prazo'     => 'date',
    ];

    public function pdi(): BelongsTo
    {
        return $this->belongsTo(Pdi::class);
    }
}
