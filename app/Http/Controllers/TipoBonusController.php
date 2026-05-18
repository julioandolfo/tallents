<?php
namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\TipoBonus;
use Illuminate\Http\Request;

class TipoBonusController extends Controller
{
    public function index(Request $request)
    {
        $tiposBonus = TipoBonus::with('empresa')
            ->when($request->empresa_id, fn($q, $v) => $q->where('empresa_id', $v))
            ->when($request->search, fn($q, $v) => $q->where('nome', 'like', "%$v%"))
            ->orderBy('nome')
            ->paginate(20)
            ->withQueryString();

        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('tipos-bonus.index', compact('tiposBonus', 'empresas'));
    }

    public function create()
    {
        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('tipos-bonus.create', compact('empresas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'nome'       => 'required|string|max:255',
            'descricao'  => 'nullable|string|max:1000',
            'percentual' => 'nullable|numeric|min:0|max:100',
            'fixo'       => 'nullable|numeric|min:0',
            'ativo'      => 'boolean',
        ]);

        $data['ativo'] = $request->boolean('ativo', true);

        TipoBonus::create($data);

        return redirect()
            ->route('tipos-bonus.index')
            ->with('success', 'Tipo de bônus cadastrado com sucesso!');
    }

    public function show(TipoBonus $tiposBonus)
    {
        $tiposBonus->load('empresa');

        return view('tipos-bonus.show', compact('tiposBonus'));
    }

    public function edit(TipoBonus $tiposBonus)
    {
        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('tipos-bonus.edit', compact('tiposBonus', 'empresas'));
    }

    public function update(Request $request, TipoBonus $tiposBonus)
    {
        $data = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'nome'       => 'required|string|max:255',
            'descricao'  => 'nullable|string|max:1000',
            'percentual' => 'nullable|numeric|min:0|max:100',
            'fixo'       => 'nullable|numeric|min:0',
            'ativo'      => 'boolean',
        ]);

        $data['ativo'] = $request->boolean('ativo');

        $tiposBonus->update($data);

        return redirect()
            ->route('tipos-bonus.index')
            ->with('success', 'Tipo de bônus atualizado com sucesso!');
    }

    public function destroy(TipoBonus $tiposBonus)
    {
        $tiposBonus->delete();

        return redirect()
            ->route('tipos-bonus.index')
            ->with('success', 'Tipo de bônus removido com sucesso!');
    }
}
