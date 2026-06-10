<?php
namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Setor;
use Illuminate\Http\Request;

class SetorController extends Controller
{
    public function index(Request $request)
    {
        $setores = Setor::with('empresa')
            ->when($request->empresa_id, fn($q, $v) => $q->where('empresa_id', $v))
            ->when($request->search, fn($q, $v) => $q->where('nome', 'like', "%$v%"))
            ->orderBy('nome')
            ->paginate(20)
            ->withQueryString();

        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('setores.index', compact('setores', 'empresas'));
    }

    public function create()
    {
        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('setores.create', compact('empresas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'nome'       => 'required|string|max:255',
            'descricao'  => 'nullable|string|max:1000',
            'ativo'      => 'boolean',
        ]);

        $data['ativo'] = $request->boolean('ativo', true);

        $setor = Setor::create($data);

        return redirect()
            ->route('setores.index')
            ->with('success', 'Setor cadastrado com sucesso!');
    }

    public function show(Setor $setor)
    {
        return redirect()->route('setores.edit', $setor);
    }

    public function edit(Setor $setor)
    {
        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('setores.edit', compact('setor', 'empresas'));
    }

    public function update(Request $request, Setor $setor)
    {
        $data = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'nome'       => 'required|string|max:255',
            'descricao'  => 'nullable|string|max:1000',
            'ativo'      => 'boolean',
        ]);

        $data['ativo'] = $request->boolean('ativo', true);

        $setor->update($data);

        return redirect()
            ->route('setores.index')
            ->with('success', 'Setor atualizado com sucesso!');
    }

    public function destroy(Setor $setor)
    {
        $setor->delete();

        return redirect()
            ->route('setores.index')
            ->with('success', 'Setor removido com sucesso!');
    }
}
