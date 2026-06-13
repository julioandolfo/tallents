<?php

namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\Empresa;
use Illuminate\Http\Request;

class OrganogramaController extends Controller
{
    public function index(Request $request)
    {
        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();
        $empresaId = $request->empresa_id ?: optional($empresas->first())->id;

        // Carrega os colaboradores ativos da empresa e monta a árvore por líder.
        $colaboradores = Colaborador::with(['cargo', 'setor'])
            ->where('status', 'ATIVO')
            ->when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))
            ->orderBy('nome')
            ->get();

        $porLider = $colaboradores->groupBy('lider_id');

        // Raízes: quem não tem líder (ou cujo líder está fora do conjunto filtrado).
        $ids = $colaboradores->pluck('id')->all();
        $raizes = $colaboradores->filter(fn($c) => ! $c->lider_id || ! in_array($c->lider_id, $ids))->values();

        return view('organograma.index', compact('empresas', 'empresaId', 'raizes', 'porLider', 'colaboradores'));
    }
}
