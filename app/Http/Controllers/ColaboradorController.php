<?php
namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\NivelHierarquico;
use App\Models\Setor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ColaboradorController extends Controller
{
    public function index(Request $request)
    {
        $colaboradores = Colaborador::with(['empresa', 'setor', 'cargo'])
            ->when($request->empresa_id, fn($q, $v) => $q->where('empresa_id', $v))
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->search, fn($q, $v) => $q->where(function ($sub) use ($v) {
                $sub->where('nome', 'like', "%$v%")
                    ->orWhere('cpf', 'like', "%$v%");
            }))
            ->orderBy('nome')
            ->paginate(20)
            ->withQueryString();

        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('colaboradores.index', compact('colaboradores', 'empresas'));
    }

    public function create()
    {
        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();
        $setores  = Setor::orderBy('nome')->get();
        $cargos   = Cargo::orderBy('nome')->get();
        $niveis   = NivelHierarquico::orderBy('nivel')->get();

        return view('colaboradores.create', compact('empresas', 'setores', 'cargos', 'niveis'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'empresa_id'     => 'required|exists:empresas,id',
            'nome'           => 'required|string|max:255',
            'cpf'            => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:255',
            'cargo_id'       => 'nullable|exists:cargos,id',
            'setor_id'       => 'nullable|exists:setores,id',
            'salario'        => 'nullable|numeric|min:0',
            'tipo_contrato'  => 'required|in:CLT,PJ,ESTAGIO,TEMPORARIO',
            'data_admissao'  => 'nullable|date',
            'foto'           => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('fotos', 'public');
        }

        if ($request->filled('senha')) {
            $data['senha_hash'] = Hash::make($request->senha);
        }

        $colaborador = Colaborador::create($data);

        return redirect()
            ->route('colaboradores.show', $colaborador)
            ->with('success', 'Colaborador cadastrado com sucesso!');
    }

    public function show(Colaborador $colaborador)
    {
        $colaborador->load([
            'empresa',
            'setor',
            'cargo',
            'nivelHierarquico',
            'lider',
            'ocorrencias.tipoOcorrencia',
            'horasExtras',
            'promocoes',
            'bonus.tipoBonus',
        ]);

        return view('colaboradores.show', compact('colaborador'));
    }

    public function edit(Colaborador $colaborador)
    {
        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();
        $setores  = Setor::where('empresa_id', $colaborador->empresa_id)->orderBy('nome')->get();
        $cargos   = Cargo::where('empresa_id', $colaborador->empresa_id)->orderBy('nome')->get();
        $niveis   = NivelHierarquico::where('empresa_id', $colaborador->empresa_id)->orderBy('nivel')->get();
        $lideres  = Colaborador::where('empresa_id', $colaborador->empresa_id)
            ->where('id', '!=', $colaborador->id)
            ->where('status', 'ATIVO')
            ->orderBy('nome')
            ->get();

        return view('colaboradores.edit', compact('colaborador', 'empresas', 'setores', 'cargos', 'niveis', 'lideres'));
    }

    public function update(Request $request, Colaborador $colaborador)
    {
        $data = $request->validate([
            'nome'     => 'required|string|max:255',
            'cpf'      => 'nullable|string|max:20',
            'email'    => 'nullable|email|max:255',
            'cargo_id' => 'nullable|exists:cargos,id',
            'setor_id' => 'nullable|exists:setores,id',
            'salario'  => 'nullable|numeric|min:0',
            'status'   => 'required|in:ATIVO,INATIVO,FERIAS,LICENCA',
            'foto'     => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($colaborador->foto) {
                Storage::disk('public')->delete($colaborador->foto);
            }
            $data['foto'] = $request->file('foto')->store('fotos', 'public');
        }

        if ($request->filled('senha')) {
            $data['senha_hash'] = Hash::make($request->senha);
        }

        $colaborador->update($data);

        return redirect()
            ->route('colaboradores.show', $colaborador)
            ->with('success', 'Colaborador atualizado!');
    }

    public function destroy(Colaborador $colaborador)
    {
        $colaborador->delete();

        return redirect()
            ->route('colaboradores.index')
            ->with('success', 'Colaborador removido!');
    }
}
