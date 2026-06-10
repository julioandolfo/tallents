<?php
namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $usuarios = Usuario::with('empresas')
            ->when($request->search, fn($q, $v) => $q->where(function ($sub) use ($v) {
                $sub->where('name', 'like', "%$v%")
                    ->orWhere('email', 'like', "%$v%");
            }))
            ->when($request->role, fn($q, $v) => $q->where('role', $v))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('usuarios.create', compact('empresas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:users,email',
            'cpf'          => 'nullable|string|max:20|unique:users,cpf',
            'password'     => 'required|string|min:8|confirmed',
            'perfil'       => 'required|string',
            'empresa_ids'  => 'nullable|array',
            'empresa_ids.*'=> 'exists:empresas,id',
            'ativo'        => 'boolean',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['role']     = $this->normalizeRole($data['perfil']);
        $data['ativo']    = $request->boolean('ativo', true);

        $empresaIds = $data['empresa_ids'] ?? [];
        unset($data['empresa_ids'], $data['perfil']);

        $usuario = Usuario::create($data);

        if (!empty($empresaIds)) {
            $usuario->empresas()->sync($empresaIds);
        }

        return redirect()
            ->route('usuarios.show', $usuario)
            ->with('success', 'Usuário criado com sucesso!');
    }

    public function show(Usuario $usuario)
    {
        $usuario->load('empresas');

        return view('usuarios.show', compact('usuario'));
    }

    public function edit(Usuario $usuario)
    {
        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('usuarios.edit', compact('usuario', 'empresas'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:users,email,' . $usuario->id,
            'cpf'          => 'nullable|string|max:20|unique:users,cpf,' . $usuario->id,
            'password'     => 'nullable|string|min:8|confirmed',
            'perfil'       => 'required|string',
            'empresa_ids'  => 'nullable|array',
            'empresa_ids.*'=> 'exists:empresas,id',
            'ativo'        => 'boolean',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['role']   = $this->normalizeRole($data['perfil']);
        $data['ativo']  = $request->boolean('ativo', true);
        $empresaIds     = $data['empresa_ids'] ?? [];
        unset($data['empresa_ids'], $data['perfil']);

        $usuario->update($data);
        $usuario->empresas()->sync($empresaIds);

        return redirect()
            ->route('usuarios.show', $usuario)
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Normaliza o valor do campo "perfil" do formulário para um dos papéis
     * válidos do enum de usuários (ADMIN, RH, GESTOR, COLABORADOR).
     */
    private function normalizeRole(string $perfil): string
    {
        $role  = strtoupper(trim($perfil));
        $valid = ['ADMIN', 'RH', 'GESTOR', 'COLABORADOR'];

        return in_array($role, $valid, true) ? $role : 'COLABORADOR';
    }

    public function destroy(Usuario $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return redirect()
                ->route('usuarios.index')
                ->with('error', 'Você não pode excluir o seu próprio usuário.');
        }

        $usuario->delete();

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuário removido com sucesso!');
    }
}
