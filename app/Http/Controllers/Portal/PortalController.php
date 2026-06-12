<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Comunicado;
use App\Models\Evento;

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

        $comunicados = $this->comunicadosVisiveis($colaborador)->take(4)->get();
        $eventos = $this->eventosVisiveis($colaborador)->proximos()->take(4)->get();

        return view('portal.index', compact('usuario', 'colaborador', 'ocorrencias', 'horasExtrasMes', 'comunicados', 'eventos'));
    }

    /** Mural completo: comunicados e próximos eventos. */
    public function mural()
    {
        $usuario = auth()->user();
        $colaborador = $usuario->colaborador()->with('empresa')->first();

        $comunicados = $this->comunicadosVisiveis($colaborador)->paginate(10);
        $eventos = $this->eventosVisiveis($colaborador)->proximos()->take(8)->get();

        return view('portal.mural', compact('comunicados', 'eventos'));
    }

    /** Comunicados publicados visíveis ao colaborador (da empresa dele ou gerais). */
    private function comunicadosVisiveis($colaborador)
    {
        return Comunicado::with('autor')
            ->publicados()
            ->when($colaborador, fn($q) => $q->where(fn($s) => $s
                ->whereNull('empresa_id')
                ->orWhere('empresa_id', $colaborador->empresa_id)))
            ->orderByDesc('destaque')
            ->orderByDesc('publicado_em')
            ->orderByDesc('id');
    }

    /** Eventos visíveis ao colaborador (da empresa dele ou gerais). */
    private function eventosVisiveis($colaborador)
    {
        return Evento::query()
            ->when($colaborador, fn($q) => $q->where(fn($s) => $s
                ->whereNull('empresa_id')
                ->orWhere('empresa_id', $colaborador->empresa_id)));
    }
}
