<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ColaboradorBonus;
use App\Models\Colaborador;
use Illuminate\Http\Request;

class BonusApiController extends Controller
{
    /**
     * Retorna os bônus do colaborador com tipo de bônus.
     */
    public function show(Colaborador $colaborador)
    {
        $bonus = $colaborador->bonus()->with('tipoBonus')->get();

        return response()->json($bonus);
    }

    /**
     * Cria ou atualiza um bônus para o colaborador.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'colaborador_id' => 'required|exists:colaboradores,id',
            'tipo_bonus_id'  => 'required|exists:tipos_bonus,id',
            'valor'          => 'required|numeric|min:0',
            'ativo'          => 'boolean',
        ]);

        $data['ativo'] = $request->boolean('ativo', true);

        $bonus = ColaboradorBonus::create($data);

        return response()->json($bonus->load('tipoBonus'), 201);
    }

    /**
     * Remove um bônus.
     */
    public function destroy(ColaboradorBonus $bonus)
    {
        $bonus->delete();

        return response()->json(['message' => 'Bônus removido com sucesso.']);
    }
}
