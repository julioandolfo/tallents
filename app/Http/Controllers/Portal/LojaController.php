<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Recompensa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LojaController extends Controller
{
    public function index()
    {
        $colaborador = auth()->user()->colaborador()->first();
        abort_unless($colaborador, 403, 'Conta não vinculada a um colaborador.');

        $saldo = $colaborador->saldoPontos();
        $recompensas = Recompensa::where('ativa', true)->orderBy('custo_pontos')->get();
        $meusResgates = $colaborador->resgates()->with('recompensa')->take(10)->get();

        return view('portal.loja', compact('colaborador', 'saldo', 'recompensas', 'meusResgates'));
    }

    public function resgatar(Request $request, Recompensa $recompensa)
    {
        $colaborador = auth()->user()->colaborador()->first();
        abort_unless($colaborador, 403);

        DB::transaction(function () use ($colaborador, $recompensa) {
            // Trava a recompensa para evitar corrida no estoque.
            $recompensa = Recompensa::whereKey($recompensa->id)->lockForUpdate()->firstOrFail();

            if (! $recompensa->disponivel()) {
                throw ValidationException::withMessages(['recompensa' => 'Recompensa indisponível.']);
            }

            if ($colaborador->saldoPontos() < $recompensa->custo_pontos) {
                throw ValidationException::withMessages(['recompensa' => 'Pontos insuficientes.']);
            }

            $colaborador->resgates()->create([
                'recompensa_id' => $recompensa->id,
                'custo_pontos'  => $recompensa->custo_pontos,
                'status'        => 'PENDENTE',
            ]);

            $colaborador->pontosMovimentacoes()->create([
                'origem'    => 'RESGATE',
                'pontos'    => -$recompensa->custo_pontos,
                'descricao' => 'Resgate: ' . $recompensa->nome,
            ]);

            if (! is_null($recompensa->estoque)) {
                $recompensa->decrement('estoque');
            }
        });

        return back()->with('success', 'Resgate solicitado! Aguarde a aprovação do RH.');
    }
}
