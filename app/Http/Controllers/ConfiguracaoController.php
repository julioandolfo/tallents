<?php
namespace App\Http\Controllers;

use App\Models\ConfiguracaoEmail;
use App\Models\Empresa;
use Illuminate\Http\Request;

class ConfiguracaoController extends Controller
{
    public function index()
    {
        $configEmail = ConfiguracaoEmail::first();
        $empresa     = Empresa::query()->orderBy('id')->first();

        // A view trabalha com um array simples de chaves "mail_*"/"empresa_*".
        $config = [
            'mail_driver'       => $configEmail->driver          ?? 'smtp',
            'mail_host'         => $configEmail->smtp_host        ?? '',
            'mail_port'         => $configEmail->smtp_port        ?? 587,
            'mail_username'     => $configEmail->smtp_username    ?? '',
            'mail_encryption'   => $configEmail->smtp_encryption  ?? 'tls',
            'mail_from_name'    => $configEmail->from_name        ?? 'Tallents Gestão',
            'mail_from_address' => $configEmail->from_email       ?? '',
            'empresa_nome'      => $empresa->nome     ?? '',
            'empresa_cnpj'      => $empresa->cnpj     ?? '',
            'empresa_telefone'  => $empresa->telefone ?? '',
        ];

        return view('configuracoes.index', compact('config', 'configEmail', 'empresa'));
    }

    public function update(Request $request)
    {
        return $request->input('secao') === 'empresa'
            ? $this->updateEmpresa($request)
            : $this->updateEmail($request);
    }

    public function updateEmail(Request $request)
    {
        $data = $request->validate([
            'mail_driver'       => 'nullable|string|max:50',
            'mail_host'         => 'nullable|string|max:255',
            'mail_port'         => 'nullable|integer|min:1|max:65535',
            'mail_username'     => 'nullable|string|max:255',
            'mail_password'     => 'nullable|string|max:255',
            'mail_encryption'   => 'nullable|string|max:20',
            'mail_from_name'    => 'nullable|string|max:255',
            'mail_from_address' => 'nullable|email|max:255',
        ]);

        $config  = ConfiguracaoEmail::first() ?? new ConfiguracaoEmail();
        $payload = [
            'driver'          => $data['mail_driver'] ?? 'smtp',
            'smtp_host'       => $data['mail_host'] ?? null,
            'smtp_port'       => $data['mail_port'] ?? 587,
            'smtp_username'   => $data['mail_username'] ?? null,
            'smtp_encryption' => $data['mail_encryption'] ?? 'tls',
            'from_email'      => $data['mail_from_address'] ?? null,
            'from_name'       => $data['mail_from_name'] ?? 'Tallents Gestão',
        ];

        // Só sobrescreve a senha quando informada (o campo fica vazio na edição).
        if ($request->filled('mail_password')) {
            $payload['smtp_password'] = $data['mail_password'];
        }

        $config->fill($payload)->save();

        return redirect()
            ->route('configuracoes.index')
            ->with('success', 'Configurações de e-mail salvas com sucesso!');
    }

    /** Envia um e-mail de teste para validar o SMTP configurado. */
    public function testarEmail(Request $request, \App\Services\EmailService $email)
    {
        $data = $request->validate(['email_teste' => 'required|email']);

        if (! $email->habilitado()) {
            return back()->with('error', 'Configure o SMTP (host e e-mail remetente) antes de testar.');
        }

        $ok = $email->enviar($data['email_teste'], new \App\Mail\EmailTeste());

        return back()->with(
            $ok ? 'success' : 'error',
            $ok ? "E-mail de teste enviado para {$data['email_teste']}." : 'Falha ao enviar — verifique as credenciais SMTP (veja os logs).'
        );
    }

    private function updateEmpresa(Request $request)
    {
        $data = $request->validate([
            'empresa_nome'     => 'required|string|max:255',
            'empresa_cnpj'     => 'nullable|string|max:20',
            'empresa_telefone' => 'nullable|string|max:20',
        ]);

        $empresa = Empresa::query()->orderBy('id')->first() ?? new Empresa();
        $empresa->fill([
            'nome'     => $data['empresa_nome'],
            'cnpj'     => $data['empresa_cnpj'] ?? $empresa->cnpj,
            'telefone' => $data['empresa_telefone'] ?? $empresa->telefone,
        ])->save();

        return redirect()
            ->route('configuracoes.index')
            ->with('success', 'Dados da empresa atualizados com sucesso!');
    }
}
