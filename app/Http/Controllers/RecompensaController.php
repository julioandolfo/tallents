<?php

namespace App\Http\Controllers;

use App\Models\Recompensa;
use Illuminate\Http\Request;

class RecompensaController extends Controller
{
    public function index()
    {
        $recompensas = Recompensa::orderByDesc('ativa')->orderBy('custo_pontos')->paginate(20);

        return view('recompensas.index', compact('recompensas'));
    }

    public function create()
    {
        return view('recompensas.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        if ($request->hasFile('imagem')) {
            $data['imagem'] = $request->file('imagem')->store('recompensas', 'public');
        }

        Recompensa::create($data);

        return redirect()->route('recompensas.index')->with('success', 'Recompensa criada!');
    }

    public function edit(Recompensa $recompensa)
    {
        return view('recompensas.edit', compact('recompensa'));
    }

    public function update(Request $request, Recompensa $recompensa)
    {
        $data = $this->validateData($request);

        if ($request->hasFile('imagem')) {
            $data['imagem'] = $request->file('imagem')->store('recompensas', 'public');
        }

        $recompensa->update($data);

        return redirect()->route('recompensas.index')->with('success', 'Recompensa atualizada!');
    }

    public function destroy(Recompensa $recompensa)
    {
        $recompensa->delete();

        return redirect()->route('recompensas.index')->with('success', 'Recompensa removida.');
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'nome'         => 'required|string|max:255',
            'descricao'    => 'nullable|string|max:1000',
            'custo_pontos' => 'required|integer|min:1',
            'estoque'      => 'nullable|integer|min:0',
            'imagem'       => 'nullable|image|max:4096',
            'ativa'        => 'nullable|boolean',
        ]);

        $data['ativa'] = $request->boolean('ativa');

        return $data;
    }
}
