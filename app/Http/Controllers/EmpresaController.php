<?php
namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmpresaController extends Controller
{
    public function index(Request $request)
    {
        $empresas = Empresa::withCount('colaboradores')
            ->when($request->search, fn($q, $v) => $q->where(function ($sub) use ($v) {
                $sub->where('nome', 'like', "%$v%")
                    ->orWhere('cnpj', 'like', "%$v%");
            }))
            ->when($request->has('ativa') && $request->ativa !== '', fn($q) => $q->where('ativa', (bool) $request->ativa))
            ->orderBy('nome')
            ->paginate(20)
            ->withQueryString();

        return view('empresas.index', compact('empresas'));
    }

    public function create()
    {
        return view('empresas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome'          => 'required|string|max:255',
            'cnpj'          => 'nullable|string|max:20',
            'razao_social'  => 'nullable|string|max:255',
            'email'         => 'nullable|email|max:255',
            'telefone'      => 'nullable|string|max:20',
            'endereco'      => 'nullable|string|max:500',
            'logo'          => 'nullable|image|max:2048',
            'ativa'         => 'boolean',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $data['ativa'] = $request->boolean('ativa', true);

        $empresa = Empresa::create($data);

        return redirect()
            ->route('empresas.show', $empresa)
            ->with('success', 'Empresa cadastrada com sucesso!');
    }

    public function show(Empresa $empresa)
    {
        $empresa->loadCount(['colaboradores', 'setores', 'cargos'])
            ->load(['setores', 'cargos']);

        $colaboradoresAtivos = $empresa->colaboradores()->where('status', 'ATIVO')->count();

        return view('empresas.show', compact('empresa', 'colaboradoresAtivos'));
    }

    public function edit(Empresa $empresa)
    {
        return view('empresas.edit', compact('empresa'));
    }

    public function update(Request $request, Empresa $empresa)
    {
        $data = $request->validate([
            'nome'         => 'required|string|max:255',
            'cnpj'         => 'nullable|string|max:20',
            'razao_social' => 'nullable|string|max:255',
            'email'        => 'nullable|email|max:255',
            'telefone'     => 'nullable|string|max:20',
            'endereco'     => 'nullable|string|max:500',
            'logo'         => 'nullable|image|max:2048',
            'ativa'        => 'boolean',
        ]);

        if ($request->hasFile('logo')) {
            if ($empresa->logo) {
                Storage::disk('public')->delete($empresa->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $data['ativa'] = $request->boolean('ativa');

        $empresa->update($data);

        return redirect()
            ->route('empresas.show', $empresa)
            ->with('success', 'Empresa atualizada com sucesso!');
    }

    public function destroy(Empresa $empresa)
    {
        if ($empresa->logo) {
            Storage::disk('public')->delete($empresa->logo);
        }

        $empresa->delete();

        return redirect()
            ->route('empresas.index')
            ->with('success', 'Empresa removida com sucesso!');
    }
}
