<?php
namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\Empresa;
use App\Models\NivelHierarquico;
use Illuminate\Http\Request;

class CargoController extends Controller
{
    public function index(Request $request)
    {
        $cargos = Cargo::with(['empresa', 'nivelHierarquico'])
            ->when($request->empresa_id, fn($q, $v) => $q->where('empresa_id', $v))
            ->when($request->search, fn($q, $v) => $q->where('nome', 'like', "%$v%"))
            ->orderBy('nome')
            ->paginate(20)
            ->withQueryString();

        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('cargos.index', compact('cargos', 'empresas'));
    }

    public function create()
    {
        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();
        $niveis   = NivelHierarquico::orderBy('nivel')->get();

        return view('cargos.create', compact('empresas', 'niveis'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'empresa_id'           => 'nullable|exists:empresas,id',
            'nome'                 => 'required|string|max:255',
            'descricao'            => 'nullable|string|max:1000',
            'salario_base'         => 'nullable|numeric|min:0',
            'salario_maximo'       => 'nullable|numeric|min:0',
            'nivel_hierarquico_id' => 'nullable|exists:niveis_hierarquicos,id',
            'ativo'                => 'boolean',
        ]);

        $data['empresa_id'] = $this->resolveEmpresaId($request);
        $data['ativo']      = $request->boolean('ativo', true);

        Cargo::create($data);

        return redirect()
            ->route('cargos.index')
            ->with('success', 'Cargo cadastrado com sucesso!');
    }

    public function show(Cargo $cargo)
    {
        return redirect()->route('cargos.edit', $cargo);
    }

    public function edit(Cargo $cargo)
    {
        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();
        $niveis   = NivelHierarquico::where('empresa_id', $cargo->empresa_id)->orderBy('nivel')->get();

        return view('cargos.edit', compact('cargo', 'empresas', 'niveis'));
    }

    public function update(Request $request, Cargo $cargo)
    {
        $data = $request->validate([
            'empresa_id'           => 'nullable|exists:empresas,id',
            'nome'                 => 'required|string|max:255',
            'descricao'            => 'nullable|string|max:1000',
            'salario_base'         => 'nullable|numeric|min:0',
            'salario_maximo'       => 'nullable|numeric|min:0',
            'nivel_hierarquico_id' => 'nullable|exists:niveis_hierarquicos,id',
            'ativo'                => 'boolean',
        ]);

        $data['empresa_id'] = $this->resolveEmpresaId($request, $cargo->empresa_id);
        $data['ativo']      = $request->boolean('ativo', true);

        $cargo->update($data);

        return redirect()
            ->route('cargos.index')
            ->with('success', 'Cargo atualizado com sucesso!');
    }

    public function destroy(Cargo $cargo)
    {
        $cargo->delete();

        return redirect()
            ->route('cargos.index')
            ->with('success', 'Cargo removido com sucesso!');
    }
}
