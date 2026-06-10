<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required',
            'password' => 'required',
        ], [
            'email.required'    => 'Informe seu e-mail ou CPF.',
            'password.required' => 'Informe sua senha.',
        ]);

        // O formulário usa o campo "email", mas o valor pode ser e-mail ou CPF.
        $login = preg_replace('/[^0-9a-zA-Z@._-]/', '', $request->input('email'));
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'cpf';

        if (Auth::attempt(
            [$field => $login, 'password' => $request->password, 'ativo' => true],
            $request->boolean('remember')
        )) {
            $request->session()->regenerate();
            Auth::user()->forceFill(['last_login_at' => now()])->save();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['email' => 'Credenciais inválidas.'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
