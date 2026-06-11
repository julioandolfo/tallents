<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;

class PortalController extends Controller
{
    public function index()
    {
        $usuario = auth()->user();
        $colaborador = $usuario->colaborador()->with(['cargo', 'setor', 'empresa'])->first();

        $ocorrencias = collect();
        $horasExtrasMes = 0;

        if ($colaborador) {
            $ocorrencias = $colaborador->ocorrencias()
                ->with('tipoOcorrencia')
                ->latest('data_ocorrencia')
                ->take(5)
                ->get();

            $horasExtrasMes = (float) $colaborador->horasExtras()
                ->whereMonth('data', now()->month)
                ->whereYear('data', now()->year)
                ->sum('horas');
        }

        return view('portal.index', compact('usuario', 'colaborador', 'ocorrencias', 'horasExtrasMes'));
    }
}
