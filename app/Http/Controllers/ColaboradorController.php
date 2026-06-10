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
        $data = $request->validate($this->rules() + [
            'empresa_id' => 'nullable|exists:empresas,id',
        ]);

        $payload = $this->mapColaborador($request, $data);
        $payload['empresa_id'] = $this->resolveEmpresaId($request);

        if ($request->hasFile('foto')) {
            $payload['foto'] = $request->file('foto')->store('fotos', 'public');
        }

        $colaborador = Colaborador::create($payload);

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
        ])->loadCount(['ocorrencias', 'horasExtras', 'promocoes']);

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
        $data = $request->validate($this->rules() + [
            'empresa_id' => 'nullable|exists:empresas,id',
        ]);

        $payload = $this->mapColaborador($request, $data);
        $payload['empresa_id'] = $this->resolveEmpresaId($request, $colaborador->empresa_id);

        if ($request->hasFile('foto')) {
            if ($colaborador->foto) {
                Storage::disk('public')->delete($colaborador->foto);
            }
            $payload['foto'] = $request->file('foto')->store('fotos', 'public');
        }

        $colaborador->update($payload);

        return redirect()
            ->route('colaboradores.show', $colaborador)
            ->with('success', 'Colaborador atualizado!');
    }

    private function rules(): array
    {
        return [
            'nome'                 => 'required|string|max:255',
            'cpf'                  => 'nullable|string|max:20',
            'rg'                   => 'nullable|string|max:30',
            'pis'                  => 'nullable|string|max:30',
            'ctps'                 => 'nullable|string|max:30',
            'matricula'            => 'nullable|string|max:50',
            'data_nascimento'      => 'nullable|date',
            'sexo'                 => 'nullable|string|max:10',
            'estado_civil'         => 'nullable|string|max:30',
            'email_pessoal'        => 'nullable|email|max:255',
            'email_login'          => 'nullable|email|max:255',
            'telefone'             => 'nullable|string|max:20',
            'cargo_id'             => 'nullable|exists:cargos,id',
            'setor_id'             => 'nullable|exists:setores,id',
            'nivel_hierarquico_id' => 'nullable|exists:niveis_hierarquicos,id',
            'salario'              => 'nullable|numeric|min:0',
            'regime_trabalho'      => 'nullable|string|max:30',
            'carga_horaria'        => 'nullable|string|max:30',
            'data_admissao'        => 'nullable|date',
            'status'               => 'nullable|string|max:30',
            'cep'                  => 'nullable|string|max:10',
            'logradouro'           => 'nullable|string|max:255',
            'numero'               => 'nullable|string|max:20',
            'complemento'          => 'nullable|string|max:255',
            'bairro'               => 'nullable|string|max:100',
            'cidade'               => 'nullable|string|max:100',
            'estado'               => 'nullable|string|max:50',
            'foto'                 => 'nullable|image|max:2048',
            'password'             => 'nullable|string|min:6|confirmed',
            'perfil'               => 'nullable|string',
        ];
    }

    /**
     * Converte os dados validados do formulário para as colunas do model,
     * mapeando os nomes de campo do form para o schema (ex.: regime_trabalho
     * → tipo_contrato) e gerando o hash da senha quando informada.
     */
    private function mapColaborador(Request $request, array $data): array
    {
        $payload = collect($data)->except(['regime_trabalho', 'password', 'perfil', 'foto'])->toArray();

        $payload['tipo_contrato'] = $data['regime_trabalho'] ?? null;
        $payload['status']        = $data['status'] ?? 'ATIVO';
        $payload['email']         = $data['email_login'] ?? $data['email_pessoal'] ?? null;

        if ($request->filled('password')) {
            $payload['senha_hash'] = Hash::make($request->input('password'));
        }

        return $payload;
    }

    public function destroy(Colaborador $colaborador)
    {
        $colaborador->delete();

        return redirect()
            ->route('colaboradores.index')
            ->with('success', 'Colaborador removido!');
    }
}
