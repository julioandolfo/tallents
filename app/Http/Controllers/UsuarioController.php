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
                $sub->where('nome', 'like', "%$v%")
                    ->orWhere('email', 'like', "%$v%");
            }))
            ->when($request->role, fn($q, $v) => $q->where('role', $v))
            ->orderBy('nome')
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
            'nome'        => 'required|string|max:255',
            'email'       => 'required|email|max:255|unique:usuarios,email',
            'cpf'         => 'nullable|string|max:20|unique:usuarios,cpf',
            'password'    => 'required|string|min:8|confirmed',
            'role'        => 'required|in:ADMIN,GESTOR,RH,VISUALIZADOR',
            'empresa_ids' => 'nullable|array',
            'empresa_ids.*'=> 'exists:empresas,id',
            'ativo'       => 'boolean',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['ativo']    = $request->boolean('ativo', true);

        $empresaIds = $data['empresa_ids'] ?? [];
        unset($data['empresa_ids']);

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
            'nome'         => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:usuarios,email,' . $usuario->id,
            'cpf'          => 'nullable|string|max:20|unique:usuarios,cpf,' . $usuario->id,
            'password'     => 'nullable|string|min:8|confirmed',
            'role'         => 'required|in:ADMIN,GESTOR,RH,VISUALIZADOR',
            'empresa_ids'  => 'nullable|array',
            'empresa_ids.*'=> 'exists:empresas,id',
            'ativo'        => 'boolean',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['ativo']  = $request->boolean('ativo');
        $empresaIds     = $data['empresa_ids'] ?? [];
        unset($data['empresa_ids']);

        $usuario->update($data);
        $usuario->empresas()->sync($empresaIds);

        return redirect()
            ->route('usuarios.show', $usuario)
            ->with('success', 'Usuário atualizado com sucesso!');
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
