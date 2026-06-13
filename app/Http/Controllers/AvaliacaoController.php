<?php

namespace App\Http\Controllers;

use App\Models\Avaliacao;
use App\Models\Colaborador;
use Illuminate\Http\Request;

class AvaliacaoController extends Controller
{
    public function index(Request $request)
    {
        $avaliacoes = Avaliacao::with(['colaborador', 'avaliador'])
            ->when($request->ciclo, fn($q, $v) => $q->where('ciclo', $v))
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->colaborador_id, fn($q, $v) => $q->where('colaborador_id', $v))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $ciclos = Avaliacao::query()->select('ciclo')->distinct()->orderByDesc('ciclo')->pluck('ciclo');

        return view('avaliacoes.index', compact('avaliacoes', 'ciclos'));
    }

    public function create(Request $request)
    {
        $colaboradores = Colaborador::where('status', 'ATIVO')->orderBy('nome')->get();
        $colaboradorId = $request->colaborador_id;

        return view('avaliacoes.create', compact('colaboradores', 'colaboradorId'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['avaliador_id'] = auth()->id();

        Avaliacao::create($data);

        return redirect()->route('avaliacoes.index')->with('success', 'Avaliação registrada!');
    }

    public function show(Avaliacao $avaliacao)
    {
        $avaliacao->load(['colaborador.cargo', 'avaliador']);

        return view('avaliacoes.show', compact('avaliacao'));
    }

    public function edit(Avaliacao $avaliacao)
    {
        $colaboradores = Colaborador::orderBy('nome')->get();

        return view('avaliacoes.edit', compact('avaliacao', 'colaboradores'));
    }

    public function update(Request $request, Avaliacao $avaliacao)
    {
        $avaliacao->update($this->validateData($request));

        return redirect()->route('avaliacoes.show', $avaliacao)->with('success', 'Avaliação atualizada!');
    }

    public function destroy(Avaliacao $avaliacao)
    {
        $avaliacao->delete();

        return redirect()->route('avaliacoes.index')->with('success', 'Avaliação removida.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'colaborador_id'  => 'required|exists:colaboradores,id',
            'ciclo'           => 'required|string|max:50',
            'tipo'            => 'required|in:AUTO,GESTOR,360',
            'nota_geral'      => 'nullable|numeric|min:0|max:10',
            'pontos_fortes'   => 'nullable|string|max:3000',
            'pontos_melhorar' => 'nullable|string|max:3000',
            'status'          => 'required|in:PENDENTE,CONCLUIDA',
            'data'            => 'nullable|date',
        ]);
    }
}
