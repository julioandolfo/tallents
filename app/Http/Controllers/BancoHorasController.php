<?php
namespace App\Http\Controllers;

use App\Models\BancoHorasMovimentacao;
use App\Models\Colaborador;
use App\Models\Empresa;
use Illuminate\Http\Request;

class BancoHorasController extends Controller
{
    public function index(Request $request)
    {
        $colaboradores = Colaborador::with('empresa')
            ->where('status', 'ATIVO')
            ->withSum(['movimentacoesBanco as creditos' => fn($q) => $q->where('tipo', 'CREDITO')], 'horas')
            ->withSum(['movimentacoesBanco as debitos' => fn($q) => $q->where('tipo', 'DEBITO')], 'horas')
            ->when($request->empresa_id, fn($q, $v) => $q->where('empresa_id', $v))
            ->when($request->search, fn($q, $v) => $q->where('nome', 'like', "%$v%"))
            ->orderBy('nome')
            ->paginate(20)
            ->withQueryString();

        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('banco-horas.index', compact('colaboradores', 'empresas'));
    }

    public function extrato(Colaborador $colaborador)
    {
        $colaborador->load(['empresa', 'cargo']);
        $movimentacoes = $colaborador->movimentacoesBanco()->with('registradoPor')->paginate(30);
        $saldo = $colaborador->saldoBancoHoras();

        return view('banco-horas.extrato', compact('colaborador', 'movimentacoes', 'saldo'));
    }

    public function lancar(Request $request, Colaborador $colaborador)
    {
        $data = $request->validate([
            'tipo'   => 'required|in:CREDITO,DEBITO',
            'horas'  => 'required|numeric|min:0.1|max:1000',
            'motivo' => 'nullable|string|max:255',
            'data'   => 'required|date',
        ]);

        $colaborador->movimentacoesBanco()->create([
            'empresa_id'     => $colaborador->empresa_id,
            'registrado_por' => auth()->id(),
            'tipo'           => $data['tipo'],
            'horas'          => $data['horas'],
            'motivo'         => $data['motivo'] ?? null,
            'data'           => $data['data'],
        ]);

        return back()->with('success', 'Lançamento registrado.');
    }

    public function remover(BancoHorasMovimentacao $movimentacao)
    {
        $colaborador = $movimentacao->colaborador;
        $movimentacao->delete();

        return redirect()
            ->route('banco-horas.extrato', $colaborador)
            ->with('success', 'Lançamento removido.');
    }
}
