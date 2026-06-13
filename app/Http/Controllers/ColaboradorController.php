<?php
namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\NivelHierarquico;
use App\Models\Setor;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ColaboradorController extends Controller
{
    public function index(Request $request)
    {
        $colaboradores = Colaborador::with(['empresa', 'setor', 'cargo'])
            ->visivelPara($request->user())
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
        $niveisHierarquicos = NivelHierarquico::orderBy('nivel')->get();
        $lideres  = Colaborador::where('status', 'ATIVO')->orderBy('nome')->get();

        return view('colaboradores.create', compact('empresas', 'setores', 'cargos', 'niveisHierarquicos', 'lideres'));
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
        $this->sincronizarAcesso($colaborador, $request);

        // E-mail de boas-vindas (best-effort; só envia se o SMTP estiver configurado).
        app(\App\Services\EmailService::class)->enviar(
            $colaborador->emailContato(),
            new \App\Mail\ColaboradorBoasVindas($colaborador->load(['empresa', 'cargo', 'setor']), $request->input('email_login'))
        );

        return redirect()
            ->route('colaboradores.show', $colaborador)
            ->with('success', 'Colaborador cadastrado com sucesso!');
    }

    public function show(Colaborador $colaborador)
    {
        abort_unless($colaborador->visivelPara(request()->user()), 403);

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
            'dependentes',
            'formacoes',
            'contratos',
        ])->loadCount(['ocorrencias', 'horasExtras', 'promocoes', 'contratos']);

        $tiposBonus = \App\Models\TipoBonus::where('ativo', true)->orderBy('nome')->get();

        return view('colaboradores.show', compact('colaborador', 'tiposBonus'));
    }

    public function edit(Colaborador $colaborador)
    {
        abort_unless($colaborador->visivelPara(request()->user()), 403);

        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();
        $setores  = Setor::where('empresa_id', $colaborador->empresa_id)->orderBy('nome')->get();
        $cargos   = Cargo::where('empresa_id', $colaborador->empresa_id)->orderBy('nome')->get();
        $niveisHierarquicos = NivelHierarquico::where('empresa_id', $colaborador->empresa_id)->orderBy('nivel')->get();
        $lideres  = Colaborador::where('empresa_id', $colaborador->empresa_id)
            ->where('id', '!=', $colaborador->id)
            ->where('status', 'ATIVO')
            ->orderBy('nome')
            ->get();

        return view('colaboradores.edit', compact('colaborador', 'empresas', 'setores', 'cargos', 'niveisHierarquicos', 'lideres'));
    }

    public function update(Request $request, Colaborador $colaborador)
    {
        abort_unless($colaborador->visivelPara($request->user()), 403);

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
        $this->sincronizarAcesso($colaborador, $request);

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
            'celular'              => 'nullable|string|max:20',
            'cargo_id'             => 'nullable|exists:cargos,id',
            'setor_id'             => 'nullable|exists:setores,id',
            'nivel_hierarquico_id' => 'nullable|exists:niveis_hierarquicos,id',
            'lider_id'             => 'nullable|exists:colaboradores,id',
            'salario'              => 'nullable|numeric|min:0',
            'regime_trabalho'      => 'nullable|string|max:30',
            'carga_horaria'        => 'nullable|string|max:30',
            'data_admissao'        => 'nullable|date',
            'data_demissao'        => 'nullable|date|after_or_equal:data_admissao',
            'status'               => 'nullable|string|max:30',
            'cep'                  => 'nullable|string|max:10',
            'logradouro'           => 'nullable|string|max:255',
            'numero'               => 'nullable|string|max:20',
            'complemento'          => 'nullable|string|max:255',
            'bairro'               => 'nullable|string|max:100',
            'cidade'               => 'nullable|string|max:100',
            'estado'               => 'nullable|string|max:50',
            'banco'                => 'nullable|string|max:100',
            'agencia'              => 'nullable|string|max:20',
            'conta'                => 'nullable|string|max:30',
            'tipo_conta'           => 'nullable|string|max:20',
            'pix'                  => 'nullable|string|max:255',
            'cnpj'                 => 'nullable|string|max:20',
            'observacoes'          => 'nullable|string|max:5000',
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

    /**
     * Cria ou atualiza a conta de acesso (users) vinculada ao colaborador,
     * conforme o perfil e o e-mail de login informados. Só age quando um
     * perfil é selecionado; mantém a senha atual se nenhuma nova for enviada.
     */
    private function sincronizarAcesso(Colaborador $colaborador, Request $request): void
    {
        $perfil = $request->input('perfil');
        $email = $request->input('email_login');

        if (! $perfil || ! $email) {
            return;
        }

        $roles = [
            'colaborador' => 'COLABORADOR',
            'gestor'      => 'GESTOR',
            'rh'          => 'RH',
            'admin'       => 'ADMIN',
        ];
        $role = $roles[strtolower($perfil)] ?? 'COLABORADOR';

        $usuario = Usuario::where('colaborador_id', $colaborador->id)
            ->orWhere('email', $email)
            ->first() ?? new Usuario();

        $usuario->name = $colaborador->nome;
        $usuario->email = $email;
        $usuario->role = $role;
        $usuario->colaborador_id = $colaborador->id;

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->input('password'));
        } elseif (! $usuario->exists) {
            // Conta nova sem senha definida: usa o hash já gravado no colaborador.
            $usuario->password = $colaborador->senha_hash ?: Hash::make(str()->random(16));
        }

        $usuario->save();
    }

    public function destroy(Colaborador $colaborador)
    {
        abort_unless($colaborador->visivelPara(request()->user()), 403);

        $colaborador->delete();

        return redirect()
            ->route('colaboradores.index')
            ->with('success', 'Colaborador removido!');
    }
}
