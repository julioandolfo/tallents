<?php

namespace App\Http\Controllers;

use App\Models\Integracao;
use Illuminate\Http\Request;

class IntegracaoController extends Controller
{
    public function index()
    {
        // Garante um registro para cada provider suportado.
        $integracoes = collect(Integracao::PROVIDERS)->keys()
            ->mapWithKeys(fn($p) => [$p => Integracao::para($p)]);

        return view('integracoes.index', compact('integracoes'));
    }

    public function update(Request $request, string $provider)
    {
        abort_unless(array_key_exists($provider, Integracao::PROVIDERS), 404);

        $integracao = Integracao::para($provider);
        $campos = array_keys(Integracao::PROVIDERS[$provider]['campos']);

        $credenciais = [];
        foreach ($campos as $campo) {
            $credenciais[$campo] = $request->input("credenciais.{$campo}");
        }

        $integracao->update([
            'ativo'       => $request->boolean('ativo'),
            'credenciais' => $credenciais,
        ]);

        return back()->with('success', Integracao::PROVIDERS[$provider]['nome'] . ' atualizada.');
    }
}
