<?php

namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\ColaboradorBonus;
use Illuminate\Http\Request;

class BonusColaboradorController extends Controller
{
    public function store(Request $request, Colaborador $colaborador)
    {
        $data = $request->validate([
            'tipo_bonus_id' => 'required|exists:tipos_bonus,id',
            'valor'         => 'required|numeric|min:0',
            'data_inicio'   => 'nullable|date',
            'data_fim'      => 'nullable|date|after_or_equal:data_inicio',
            'observacoes'   => 'nullable|string|max:500',
            'ativo'         => 'nullable|boolean',
        ]);

        $colaborador->bonus()->create([
            'tipo_bonus_id' => $data['tipo_bonus_id'],
            'valor'         => $data['valor'],
            'data_inicio'   => $data['data_inicio'] ?? null,
            'data_fim'      => $data['data_fim'] ?? null,
            'observacoes'   => $data['observacoes'] ?? null,
            'ativo'         => $request->boolean('ativo', true),
        ]);

        return back()->with('success', 'Bônus adicionado.');
    }

    public function destroy(ColaboradorBonus $bonus)
    {
        $colaboradorId = $bonus->colaborador_id;
        $bonus->delete();

        return redirect()->route('colaboradores.show', $colaboradorId)->with('success', 'Bônus removido.');
    }
}
