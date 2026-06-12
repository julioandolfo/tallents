<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversa extends Model
{
    protected $table = 'conversas';

    protected $fillable = [
        'user_one_id', 'user_two_id', 'ultima_mensagem_em',
    ];

    protected $casts = [
        'ultima_mensagem_em' => 'datetime',
    ];

    public function userOne(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'user_one_id');
    }

    public function userTwo(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'user_two_id');
    }

    public function mensagens(): HasMany
    {
        return $this->hasMany(Mensagem::class)->orderBy('id');
    }

    /** Retorna o outro participante da conversa em relação ao usuário dado. */
    public function outro(int $userId): ?Usuario
    {
        return $this->user_one_id === $userId ? $this->userTwo : $this->userOne;
    }

    public function envolve(int $userId): bool
    {
        return $this->user_one_id === $userId || $this->user_two_id === $userId;
    }

    /** Localiza (ou cria) a conversa entre dois usuários, normalizando a ordem. */
    public static function entre(int $a, int $b): self
    {
        [$one, $two] = $a < $b ? [$a, $b] : [$b, $a];

        return static::firstOrCreate(['user_one_id' => $one, 'user_two_id' => $two]);
    }
}
