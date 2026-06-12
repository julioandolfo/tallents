<?php

namespace App\Http\Controllers;

use App\Models\Recompensa;
use App\Models\Resgate;
use Illuminate\Http\Request;

class ResgateController extends Controller
{
    public function index(Request $request)
    {
        $resgates = Resgate::with(['colaborador', 'recompensa'])
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('resgates.index', compact('resgates'));
    }

    /** Atualiza o status do resgate; ao cancelar, devolve os pontos e o estoque. */
    public function update(Request $request, Resgate $resgate)
    {
        $data = $request->validate([
            'status' => 'required|in:PENDENTE,APROVADO,ENTREGUE,CANCELADO',
        ]);

        $anterior = $resgate->status;
        $novo = $data['status'];

        if ($novo === 'CANCELADO' && $anterior !== 'CANCELADO') {
            // Estorna os pontos e devolve o item ao estoque.
            $resgate->colaborador->pontosMovimentacoes()->create([
                'registrado_por' => auth()->id(),
                'origem'         => 'AJUSTE',
                'pontos'         => $resgate->custo_pontos,
                'descricao'      => 'Estorno de resgate cancelado',
            ]);

            if ($resgate->recompensa && ! is_null($resgate->recompensa->estoque)) {
                $resgate->recompensa->increment('estoque');
            }
        }

        $resgate->update(['status' => $novo]);

        return back()->with('success', 'Resgate atualizado.');
    }
}
