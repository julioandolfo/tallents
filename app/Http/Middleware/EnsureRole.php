<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restringe o acesso conforme o papel do usuário (coluna `role`).
 * Uso: ->middleware('papel:ADMIN,RH,GESTOR')
 *
 * Colaboradores que tentam acessar a área administrativa são levados ao
 * Portal do Colaborador em vez de receberem 403.
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! in_array($user->role, $roles, true)) {
            if ($user->role === 'COLABORADOR') {
                return redirect()->route('portal.index');
            }
            abort(403, 'Você não tem permissão para acessar esta área.');
        }

        return $next($request);
    }
}
