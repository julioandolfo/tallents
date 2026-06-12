<?php

namespace App\Http\Controllers;

use App\Models\Candidato;
use App\Models\Vaga;
use Illuminate\Http\Request;

class CandidatoController extends Controller
{
    public function store(Request $request, Vaga $vaga)
    {
        $data = $request->validate([
            'nome'        => 'required|string|max:255',
            'email'       => 'nullable|email|max:255',
            'telefone'    => 'nullable|string|max:30',
            'linkedin'    => 'nullable|string|max:255',
            'curriculo'   => 'nullable|file|max:10240',
            'etapa'       => 'nullable|in:TRIAGEM,ENTREVISTA,TESTE,PROPOSTA,CONTRATADO,REPROVADO',
            'nota'        => 'nullable|integer|min:0|max:10',
            'observacoes' => 'nullable|string|max:2000',
        ]);

        if ($request->hasFile('curriculo')) {
            $data['curriculo'] = $request->file('curriculo')->store('curriculos', 'public');
        }

        $data['etapa'] = $data['etapa'] ?? 'TRIAGEM';

        $vaga->candidatos()->create($data);

        return back()->with('success', 'Candidato adicionado.');
    }

    /** Move o candidato de etapa (usado no funil/kanban). */
    public function mover(Request $request, Candidato $candidato)
    {
        $data = $request->validate([
            'etapa' => 'required|in:TRIAGEM,ENTREVISTA,TESTE,PROPOSTA,CONTRATADO,REPROVADO',
        ]);

        $candidato->update(['etapa' => $data['etapa']]);

        return back()->with('success', 'Candidato movido para ' . $candidato->etapaLabel() . '.');
    }

    public function update(Request $request, Candidato $candidato)
    {
        $data = $request->validate([
            'nome'        => 'required|string|max:255',
            'email'       => 'nullable|email|max:255',
            'telefone'    => 'nullable|string|max:30',
            'linkedin'    => 'nullable|string|max:255',
            'nota'        => 'nullable|integer|min:0|max:10',
            'observacoes' => 'nullable|string|max:2000',
        ]);

        $candidato->update($data);

        return back()->with('success', 'Candidato atualizado.');
    }

    public function destroy(Candidato $candidato)
    {
        $vagaId = $candidato->vaga_id;
        $candidato->delete();

        return redirect()->route('vagas.show', $vagaId)->with('success', 'Candidato removido.');
    }
}
