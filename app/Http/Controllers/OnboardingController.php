<?php

namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\Onboarding;
use App\Models\OnboardingTarefa;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function index(Request $request)
    {
        $onboardings = Onboarding::with(['colaborador.empresa', 'responsavel'])
            ->withCount([
                'tarefas',
                'tarefas as tarefas_concluidas_count' => fn($q) => $q->where('concluida', true),
            ])
            ->when($request->empresa_id, fn($q, $v) => $q->where('empresa_id', $v))
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->orderByDesc('data_inicio')
            ->paginate(20)
            ->withQueryString();

        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('onboarding.index', compact('onboardings', 'empresas'));
    }

    public function create(Request $request)
    {
        $colaboradores = Colaborador::where('status', 'ATIVO')->orderBy('nome')->get();
        $colaboradorId = $request->colaborador_id;

        return view('onboarding.create', compact('colaboradores', 'colaboradorId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'colaborador_id' => 'required|exists:colaboradores,id',
            'data_inicio'    => 'required|date',
            'observacoes'    => 'nullable|string|max:2000',
            'usar_padrao'    => 'nullable|boolean',
        ]);

        $colaborador = Colaborador::findOrFail($data['colaborador_id']);

        $onboarding = Onboarding::create([
            'colaborador_id' => $colaborador->id,
            'empresa_id'     => $colaborador->empresa_id,
            'responsavel_id' => auth()->id(),
            'data_inicio'    => $data['data_inicio'],
            'status'         => 'EM_ANDAMENTO',
            'observacoes'    => $data['observacoes'] ?? null,
        ]);

        // Cria o checklist padrão, salvo quando explicitamente desativado.
        if ($request->boolean('usar_padrao', true)) {
            foreach (Onboarding::TAREFAS_PADRAO as $i => $titulo) {
                $onboarding->tarefas()->create(['titulo' => $titulo, 'ordem' => $i]);
            }
        }

        return redirect()->route('onboarding.show', $onboarding)
            ->with('success', 'Onboarding iniciado com sucesso!');
    }

    public function show(Onboarding $onboarding)
    {
        $onboarding->load(['colaborador.empresa', 'colaborador.cargo', 'responsavel', 'tarefas']);

        return view('onboarding.show', compact('onboarding'));
    }

    public function update(Request $request, Onboarding $onboarding)
    {
        $data = $request->validate([
            'status'      => 'required|in:EM_ANDAMENTO,CONCLUIDO,CANCELADO',
            'observacoes' => 'nullable|string|max:2000',
        ]);

        $onboarding->update($data);

        return back()->with('success', 'Onboarding atualizado.');
    }

    public function destroy(Onboarding $onboarding)
    {
        $onboarding->delete();

        return redirect()->route('onboarding.index')
            ->with('success', 'Onboarding removido.');
    }

    /** Adiciona uma tarefa avulsa ao checklist. */
    public function adicionarTarefa(Request $request, Onboarding $onboarding)
    {
        $data = $request->validate([
            'titulo'    => 'required|string|max:255',
            'descricao' => 'nullable|string|max:1000',
        ]);

        $onboarding->tarefas()->create([
            'titulo'    => $data['titulo'],
            'descricao' => $data['descricao'] ?? null,
            'ordem'     => (int) $onboarding->tarefas()->max('ordem') + 1,
        ]);

        return back()->with('success', 'Tarefa adicionada.');
    }

    /** Marca/desmarca uma tarefa do checklist. */
    public function alternarTarefa(OnboardingTarefa $tarefa)
    {
        $tarefa->update([
            'concluida'    => ! $tarefa->concluida,
            'concluida_em' => ! $tarefa->concluida ? now() : null,
        ]);

        // Conclui o onboarding automaticamente quando todas as tarefas terminam.
        $onboarding = $tarefa->onboarding;
        if ($onboarding && $onboarding->status === 'EM_ANDAMENTO' && $onboarding->progresso() === 100) {
            $onboarding->update(['status' => 'CONCLUIDO']);
        }

        return back();
    }

    public function removerTarefa(OnboardingTarefa $tarefa)
    {
        $tarefa->delete();

        return back()->with('success', 'Tarefa removida.');
    }
}
