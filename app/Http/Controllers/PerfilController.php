<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class PerfilController extends Controller
{
    public function index()
    {
        $usuario = auth()->user();

        return view('perfil.index', compact('usuario'));
    }

    public function update(Request $request)
    {
        $usuario = auth()->user();

        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $usuario->id,
            'foto'  => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($usuario->foto) {
                Storage::disk('public')->delete($usuario->foto);
            }
            $data['foto'] = $request->file('foto')->store('perfis', 'public');
        }

        $usuario->update($data);

        return redirect()
            ->route('perfil.index')
            ->with('success', 'Perfil atualizado com sucesso!');
    }

    public function updateSenha(Request $request)
    {
        $request->validate([
            'senha_atual' => 'required|string',
            'password'    => ['required', 'confirmed', Password::min(8)],
        ], [
            'senha_atual.required' => 'Informe sua senha atual.',
            'password.required'    => 'Informe a nova senha.',
            'password.confirmed'   => 'A confirmação da nova senha não corresponde.',
        ]);

        $usuario = auth()->user();

        if (!Hash::check($request->senha_atual, $usuario->password)) {
            return back()->withErrors(['senha_atual' => 'A senha atual está incorreta.']);
        }

        $usuario->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()
            ->route('perfil.index')
            ->with('success', 'Senha alterada com sucesso!');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ], [
            'password.required' => 'Informe sua senha para confirmar a exclusão.',
        ]);

        $usuario = auth()->user();

        if (! Hash::check($request->password, $usuario->password)) {
            return back()->withErrors(['password' => 'Senha incorreta.']);
        }

        Auth::logout();
        $usuario->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Conta excluída.');
    }
}
