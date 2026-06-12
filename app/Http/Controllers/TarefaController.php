<?php

namespace App\Http\Controllers;

use App\Models\Quadro;
use App\Models\Tarefa;
use Illuminate\Http\Request;

class TarefaController extends Controller
{
    public function store(Request $request, Quadro $quadro)
    {
        $data = $request->validate([
            'titulo'         => 'required|string|max:255',
            'descricao'      => 'nullable|string|max:2000',
            'status'         => 'nullable|in:A_FAZER,FAZENDO,CONCLUIDO',
            'prioridade'     => 'nullable|in:BAIXA,MEDIA,ALTA',
            'responsavel_id' => 'nullable|exists:users,id',
            'prazo'          => 'nullable|date',
        ]);

        $data['status'] = $data['status'] ?? 'A_FAZER';
        $data['prioridade'] = $data['prioridade'] ?? 'MEDIA';
        $data['ordem'] = (int) $quadro->tarefas()->max('ordem') + 1;

        $quadro->tarefas()->create($data);

        return back()->with('success', 'Tarefa criada.');
    }

    public function mover(Request $request, Tarefa $tarefa)
    {
        $data = $request->validate(['status' => 'required|in:A_FAZER,FAZENDO,CONCLUIDO']);

        $tarefa->update(['status' => $data['status']]);

        return back();
    }

    public function update(Request $request, Tarefa $tarefa)
    {
        $data = $request->validate([
            'titulo'         => 'required|string|max:255',
            'descricao'      => 'nullable|string|max:2000',
            'prioridade'     => 'required|in:BAIXA,MEDIA,ALTA',
            'responsavel_id' => 'nullable|exists:users,id',
            'prazo'          => 'nullable|date',
        ]);

        $tarefa->update($data);

        return back()->with('success', 'Tarefa atualizada.');
    }

    public function destroy(Tarefa $tarefa)
    {
        $quadroId = $tarefa->quadro_id;
        $tarefa->delete();

        return redirect()->route('quadros.show', $quadroId)->with('success', 'Tarefa removida.');
    }
}
