<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setor;
use Illuminate\Http\Request;

class SetorApiController extends Controller
{
    /**
     * Lista setores filtrados por empresa_id.
     */
    public function index(Request $request)
    {
        $setores = Setor::select('id', 'nome', 'empresa_id')
            ->when($request->empresa_id, fn($q, $v) => $q->where('empresa_id', $v))
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        return response()->json($setores);
    }
}
