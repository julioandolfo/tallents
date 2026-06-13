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

        return view('tipos-bonus.index', [
            'tipos'      => $tiposBonus,
            'tiposBonus' => $tiposBonus,
            'empresas'   => $empresas,
        ]);
    }

    public function create()
    {
        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('tipos-bonus.create', compact('empresas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'empresa_id'   => 'nullable|exists:empresas,id',
            'nome'         => 'required|string|max:255',
            'descricao'    => 'nullable|string|max:1000',
            'tipo_calculo' => 'nullable|string|max:50',
            'percentual'   => 'nullable|numeric|min:0|max:100',
            'fixo'         => 'nullable|numeric|min:0',
            'ativo'        => 'boolean',
        ]);

        $data['empresa_id'] = $this->resolveEmpresaId($request);
        $data['ativo']      = $request->boolean('ativo', true);

        TipoBonus::create($data);

        return redirect()
            ->route('tipos-bonus.index')
            ->with('success', 'Tipo de bônus cadastrado com sucesso!');
    }

    public function show(TipoBonus $tiposBonus)
    {
        return redirect()->route('tipos-bonus.edit', $tiposBonus);
    }

    public function edit(TipoBonus $tiposBonus)
    {
        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('tipos-bonus.edit', compact('tiposBonus', 'empresas'));
    }

    public function update(Request $request, TipoBonus $tiposBonus)
    {
        $data = $request->validate([
            'empresa_id'   => 'nullable|exists:empresas,id',
            'nome'         => 'required|string|max:255',
            'descricao'    => 'nullable|string|max:1000',
            'tipo_calculo' => 'nullable|string|max:50',
            'percentual'   => 'nullable|numeric|min:0|max:100',
            'fixo'         => 'nullable|numeric|min:0',
            'ativo'        => 'boolean',
        ]);

        $data['empresa_id'] = $this->resolveEmpresaId($request, $tiposBonus->empresa_id);
        $data['ativo']      = $request->boolean('ativo', true);

        $tiposBonus->update($data);

        return redirect()
            ->route('tipos-bonus.index')
            ->with('success', 'Tipo de bônus atualizado com sucesso!');
    }

    public function destroy(TipoBonus $tiposBonus)
    {
        if (\App\Models\ColaboradorBonus::where('tipo_bonus_id', $tiposBonus->id)->exists()) {
            return redirect()
                ->route('tipos-bonus.index')
                ->with('error', 'Não é possível remover: há bônus de colaboradores usando este tipo.');
        }

        $tiposBonus->delete();

        return redirect()
            ->route('tipos-bonus.index')
            ->with('success', 'Tipo de bônus removido com sucesso!');
    }
}
