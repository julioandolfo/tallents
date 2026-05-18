<?php
namespace App\Http\Controllers;

use App\Models\ConfiguracaoEmail;
use Illuminate\Http\Request;

class ConfiguracaoController extends Controller
{
    public function index()
    {
        $configEmail = ConfiguracaoEmail::first();

        return view('configuracoes.index', compact('configEmail'));
    }

    public function updateEmail(Request $request)
    {
        $data = $request->validate([
            'driver'     => 'required|in:smtp,sendmail,mailgun,ses,postmark',
            'host'       => 'required|string|max:255',
            'port'       => 'required|integer|min:1|max:65535',
            'encryption' => 'nullable|in:tls,ssl',
            'username'   => 'required|string|max:255',
            'password'   => 'nullable|string|max:255',
            'from_address'=> 'required|email|max:255',
            'from_name'  => 'required|string|max:255',
        ]);

        ConfiguracaoEmail::updateOrCreate(
            [],       // sem condição, apenas um registro global
            $data
        );

        return redirect()
            ->route('configuracoes.index')
            ->with('success', 'Configurações de e-mail salvas com sucesso!');
    }
}
