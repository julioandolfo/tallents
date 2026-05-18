<?php
namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\NivelHierarquico;
use Illuminate\Http\Request;

class NivelHierarquicoController extends Controller
{
    public function index(Request $request)
    {
        $niveis = NivelHierarquico::with('empresa')
            ->when($request->empresa_id, fn($q, $v) => $q->where('empresa_id', $v))
            ->when($request->search, fn($q, $v) => $q->where('nome', 'like', "%$v%"))
            ->orderBy('empresa_id')
            ->orderBy('nivel')
            ->paginate(20)
            ->withQueryString();

        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('niveis-hierarquicos.index', compact('niveis', 'empresas'));
    }

    public function create()
    {
        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('niveis-hierarquicos.create', compact('empresas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'nome'       => 'required|string|max:255',
            'nivel'      => 'required|integer|min:1',
            'descricao'  => 'nullable|string|max:1000',
        ]);

        NivelHierarquico::create($data);

        return redirect()
            ->route('niveis-hierarquicos.index')
            ->with('success', 'Nível hierárquico cadastrado com sucesso!');
    }

    public function show(NivelHierarquico $niveisHierarquico)
    {
        $niveisHierarquico->load(['empresa', 'cargos']);

        return view('niveis-hierarquicos.show', compact('niveisHierarquico'));
    }

    public function edit(NivelHierarquico $niveisHierarquico)
    {
        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('niveis-hierarquicos.edit', compact('niveisHierarquico', 'empresas'));
    }

    public function update(Request $request, NivelHierarquico $niveisHierarquico)
    {
        $data = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'nome'       => 'required|string|max:255',
            'nivel'      => 'required|integer|min:1',
            'descricao'  => 'nullable|string|max:1000',
        ]);

        $niveisHierarquico->update($data);

        return redirect()
            ->route('niveis-hierarquicos.index')
            ->with('success', 'Nível hierárquico atualizado com sucesso!');
    }

    public function destroy(NivelHierarquico $niveisHierarquico)
    {
        $niveisHierarquico->delete();

        return redirect()
            ->route('niveis-hierarquicos.index')
            ->with('success', 'Nível hierárquico removido com sucesso!');
    }
}
