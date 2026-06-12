<?php

namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\Formacao;
use Illuminate\Http\Request;

class FormacaoController extends Controller
{
    public function store(Request $request, Colaborador $colaborador)
    {
        $data = $request->validate([
            'nivel'         => 'required|in:FUNDAMENTAL,MEDIO,TECNICO,GRADUACAO,POS,MESTRADO,DOUTORADO,CURSO',
            'curso'         => 'required|string|max:255',
            'instituicao'   => 'nullable|string|max:255',
            'situacao'      => 'required|in:CURSANDO,CONCLUIDO,TRANCADO',
            'ano_conclusao' => 'nullable|integer|min:1950|max:2100',
        ]);

        $colaborador->formacoes()->create($data);

        return back()->with('success', 'Formação adicionada.');
    }

    public function destroy(Formacao $formacao)
    {
        $colaboradorId = $formacao->colaborador_id;
        $formacao->delete();

        return redirect()->route('colaboradores.show', $colaboradorId)
            ->with('success', 'Formação removida.');
    }
}
