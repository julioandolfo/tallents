<?php

namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\PontoMovimentacao;
use Illuminate\Http\Request;

class PontoController extends Controller
{
    public function index(Request $request)
    {
        // Ranking de colaboradores por saldo de pontos.
        $colaboradores = Colaborador::query()
            ->where('status', 'ATIVO')
            ->withSum('pontosMovimentacoes as saldo_pontos', 'pontos')
            ->when($request->search, fn($q, $v) => $q->where('nome', 'like', "%{$v}%"))
            ->orderByDesc('saldo_pontos')
            ->paginate(20)
            ->withQueryString();

        return view('pontos.index', compact('colaboradores'));
    }

    public function extrato(Colaborador $colaborador)
    {
        $movimentacoes = $colaborador->pontosMovimentacoes()->with('registradoPor')->paginate(20);
        $saldo = $colaborador->saldoPontos();

        return view('pontos.extrato', compact('colaborador', 'movimentacoes', 'saldo'));
    }

    public function lancar(Request $request, Colaborador $colaborador)
    {
        $data = $request->validate([
            'pontos'    => 'required|integer',
            'origem'    => 'required|in:MANUAL,ELOGIO,AVALIACAO,META,AJUSTE',
            'descricao' => 'nullable|string|max:255',
        ]);

        $colaborador->pontosMovimentacoes()->create([
            'registrado_por' => auth()->id(),
            'origem'         => $data['origem'],
            'pontos'         => $data['pontos'],
            'descricao'      => $data['descricao'] ?? null,
        ]);

        return back()->with('success', 'Pontos lançados.');
    }

    public function remover(PontoMovimentacao $movimentacao)
    {
        $colaboradorId = $movimentacao->colaborador_id;
        $movimentacao->delete();

        return redirect()->route('pontos.extrato', $colaboradorId)->with('success', 'Lançamento removido.');
    }
}
