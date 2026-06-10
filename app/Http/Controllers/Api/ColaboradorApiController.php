<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Colaborador;
use Illuminate\Http\Request;

class ColaboradorApiController extends Controller
{
    /**
     * Lista colaboradores ativos filtrados por empresa_id.
     */
    public function index(Request $request)
    {
        $colaboradores = Colaborador::select('id', 'nome', 'cargo_id', 'setor_id', 'empresa_id')
            ->when($request->empresa_id, fn($q, $v) => $q->where('empresa_id', $v))
            ->where('status', 'ATIVO')
            ->orderBy('nome')
            ->get();

        return response()->json($colaboradores);
    }

    /**
     * Retorna colaboradores ativos que podem ser líderes (mesma empresa).
     */
    public function lideres(Request $request)
    {
        $request->validate([
            'empresa_id'     => 'required|exists:empresas,id',
            'excluir_id'     => 'nullable|exists:colaboradores,id',
        ]);

        $lideres = Colaborador::select('id', 'nome')
            ->where('empresa_id', $request->empresa_id)
            ->where('status', 'ATIVO')
            ->when($request->excluir_id, fn($q, $v) => $q->where('id', '!=', $v))
            ->orderBy('nome')
            ->get();

        return response()->json($lideres);
    }
}
