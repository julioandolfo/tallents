<?php

namespace App\Http\Controllers;

use App\Models\Quadro;
use Illuminate\Http\Request;

class QuadroController extends Controller
{
    public function index()
    {
        $quadros = Quadro::withCount('tarefas')->orderByDesc('id')->get();

        return view('quadros.index', compact('quadros'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome'      => 'required|string|max:255',
            'descricao' => 'nullable|string|max:1000',
        ]);

        $data['criado_por'] = auth()->id();

        $quadro = Quadro::create($data);

        return redirect()->route('quadros.show', $quadro)->with('success', 'Quadro criado!');
    }

    public function show(Quadro $quadro)
    {
        $quadro->load('tarefas.responsavel');
        $tarefas = $quadro->tarefas->groupBy('status');
        $usuarios = \App\Models\Usuario::orderBy('name')->get();

        return view('quadros.show', compact('quadro', 'tarefas', 'usuarios'));
    }

    public function destroy(Quadro $quadro)
    {
        $quadro->delete();

        return redirect()->route('quadros.index')->with('success', 'Quadro removido.');
    }
}
