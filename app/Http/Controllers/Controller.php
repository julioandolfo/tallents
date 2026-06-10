<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;

abstract class Controller
{
    /**
     * Resolve o empresa_id de um request. Quando o formulário não envia o
     * campo (instalações de empresa única), recai para a empresa do usuário
     * autenticado e, em último caso, para a primeira empresa cadastrada.
     */
    protected function resolveEmpresaId(Request $request, ?int $fallback = null): ?int
    {
        return $request->input('empresa_id')
            ?: $fallback
            ?: optional($request->user())->empresa_id
            ?: Empresa::query()->value('id');
    }
}
