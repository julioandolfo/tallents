<?php

namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\Pdi;
use App\Models\PdiAcao;
use Illuminate\Http\Request;

class PdiController extends Controller
{
    public function index(Request $request)
    {
        $pdis = Pdi::with('colaborador')
            ->withCount([
                'acoes',
                'acoes as acoes_concluidas_count' => fn($q) => $q->where('concluida', true),
            ])
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->colaborador_id, fn($q, $v) => $q->where('colaborador_id', $v))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('pdis.index', compact('pdis'));
    }

    public function create(Request $request)
    {
        $colaboradores = Colaborador::where('status', 'ATIVO')->orderBy('nome')->get();
        $colaboradorId = $request->colaborador_id;

        return view('pdis.create', compact('colaboradores', 'colaboradorId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'colaborador_id' => 'required|exists:colaboradores,id',
            'titulo'         => 'required|string|max:255',
            'descricao'      => 'nullable|string|max:3000',
            'prazo'          => 'nullable|date',
        ]);

        $data['responsavel_id'] = auth()->id();
        $data['status'] = 'EM_ANDAMENTO';

        $pdi = Pdi::create($data);

        return redirect()->route('pdis.show', $pdi)->with('success', 'PDI criado!');
    }

    public function show(Pdi $pdi)
    {
        $pdi->load(['colaborador.cargo', 'responsavel', 'acoes']);

        return view('pdis.show', compact('pdi'));
    }

    public function update(Request $request, Pdi $pdi)
    {
        $data = $request->validate([
            'status'    => 'required|in:EM_ANDAMENTO,CONCLUIDO,CANCELADO',
            'descricao' => 'nullable|string|max:3000',
            'prazo'     => 'nullable|date',
        ]);

        $pdi->update($data);

        return back()->with('success', 'PDI atualizado.');
    }

    public function destroy(Pdi $pdi)
    {
        $pdi->delete();

        return redirect()->route('pdis.index')->with('success', 'PDI removido.');
    }

    public function adicionarAcao(Request $request, Pdi $pdi)
    {
        $data = $request->validate([
            'descricao' => 'required|string|max:500',
            'prazo'     => 'nullable|date',
        ]);

        $pdi->acoes()->create($data);

        return back()->with('success', 'Ação adicionada.');
    }

    public function alternarAcao(PdiAcao $acao)
    {
        $acao->update(['concluida' => ! $acao->concluida]);

        $pdi = $acao->pdi;
        if ($pdi && $pdi->status === 'EM_ANDAMENTO' && $pdi->progresso() === 100) {
            $pdi->update(['status' => 'CONCLUIDO']);
        }

        return back();
    }

    public function removerAcao(PdiAcao $acao)
    {
        $acao->delete();

        return back()->with('success', 'Ação removida.');
    }
}
