<?php

namespace App\Models\Concerns;

use App\Models\Usuario;

/**
 * Restringe registros (que possuem empresa_id + relação colaborador com setor_id)
 * conforme o papel do usuário:
 *  - ADMIN: tudo
 *  - RH: apenas a sua empresa
 *  - GESTOR: apenas o seu setor
 *  - COLABORADOR: apenas os seus próprios registros
 */
trait EscopoPorColaborador
{
    public function scopeVisivelPara($query, ?Usuario $user)
    {
        if (! $user || $user->isAdmin()) {
            return $query;
        }

        if ($user->isRh()) {
            return $query->where($this->getTable() . '.empresa_id', $user->empresa_id);
        }

        if ($user->isGestor()) {
            return $query->whereHas('colaborador', fn($s) => $s->where('setor_id', $user->setor_id));
        }

        if ($user->isColaborador()) {
            return $query->where($this->getTable() . '.colaborador_id', $user->colaborador_id);
        }

        return $query->whereRaw('1 = 0');
    }

    /** O usuário pode ver/abrir este registro específico? */
    public function visivelPara(?Usuario $user): bool
    {
        if (! $user || $user->isAdmin()) {
            return true;
        }

        return static::query()->whereKey($this->getKey())->visivelPara($user)->exists();
    }
}
