<?php

namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\Dependente;
use Illuminate\Http\Request;

class DependenteController extends Controller
{
    public function store(Request $request, Colaborador $colaborador)
    {
        $data = $request->validate([
            'nome'            => 'required|string|max:255',
            'parentesco'      => 'required|in:FILHO,CONJUGE,PAI,MAE,OUTRO',
            'data_nascimento' => 'nullable|date',
            'cpf'             => 'nullable|string|max:20',
            'dependente_ir'   => 'nullable|boolean',
            'observacoes'     => 'nullable|string|max:1000',
        ]);

        $colaborador->dependentes()->create([
            'nome'            => $data['nome'],
            'parentesco'      => $data['parentesco'],
            'data_nascimento' => $data['data_nascimento'] ?? null,
            'cpf'             => $data['cpf'] ?? null,
            'dependente_ir'   => $request->boolean('dependente_ir'),
            'observacoes'     => $data['observacoes'] ?? null,
        ]);

        return back()->with('success', 'Dependente adicionado.');
    }

    public function destroy(Dependente $dependente)
    {
        $colaboradorId = $dependente->colaborador_id;
        $dependente->delete();

        return redirect()->route('colaboradores.show', $colaboradorId)
            ->with('success', 'Dependente removido.');
    }
}
