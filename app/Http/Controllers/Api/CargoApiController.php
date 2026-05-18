<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cargo;
use Illuminate\Http\Request;

class CargoApiController extends Controller
{
    /**
     * Lista cargos filtrados por empresa_id.
     */
    public function index(Request $request)
    {
        $cargos = Cargo::select('id', 'nome', 'salario_base', 'empresa_id', 'nivel_hierarquico_id')
            ->when($request->empresa_id, fn($q, $v) => $q->where('empresa_id', $v))
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        return response()->json($cargos);
    }
}
