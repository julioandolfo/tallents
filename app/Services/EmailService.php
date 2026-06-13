<?php

namespace App\Services;

use App\Models\ConfiguracaoEmail;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Centraliza o envio de e-mails transacionais aplicando, em tempo de execução,
 * as credenciais SMTP gravadas no banco (tela Configurações → E-mail).
 *
 * O envio é "best-effort": se o SMTP não estiver configurado ou ocorrer
 * qualquer erro, o método registra no log e segue sem quebrar a ação principal.
 */
class EmailService
{
    /** Indica se há configuração SMTP mínima para envio. */
    public function habilitado(): bool
    {
        $cfg = ConfiguracaoEmail::first();

        return $cfg && filled($cfg->smtp_host) && filled($cfg->from_email);
    }

    /** Aplica as credenciais do banco à configuração do mailer em runtime. */
    public function configurar(): bool
    {
        $cfg = ConfiguracaoEmail::first();

        if (! $cfg || blank($cfg->smtp_host)) {
            return false;
        }

        config([
            'mail.default'                   => $cfg->driver ?: 'smtp',
            'mail.mailers.smtp.host'         => $cfg->smtp_host,
            'mail.mailers.smtp.port'         => $cfg->smtp_port ?: 587,
            'mail.mailers.smtp.username'     => $cfg->smtp_username,
            'mail.mailers.smtp.password'     => $cfg->smtp_password,
            'mail.mailers.smtp.encryption'   => $cfg->smtp_encryption ?: 'tls',
            'mail.from.address'              => $cfg->from_email,
            'mail.from.name'                 => $cfg->from_name ?: config('app.name'),
        ]);

        // Força o mailer a ser reconstruído com a nova configuração.
        try {
            Mail::purge('smtp');
        } catch (\Throwable $e) {
            // Sob Mail::fake() ou mailer não resolvido ainda: ignora.
        }

        return true;
    }

    /**
     * Envia um Mailable para o destinatário, aplicando o SMTP do banco.
     * Retorna true se enviou, false se pulou/falhou (sem lançar exceção).
     */
    public function enviar(?string $para, Mailable $mailable): bool
    {
        if (blank($para) || ! filter_var($para, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if (! $this->configurar()) {
            Log::info('E-mail não enviado: SMTP não configurado.', ['para' => $para]);

            return false;
        }

        try {
            Mail::to($para)->send($mailable);

            return true;
        } catch (\Throwable $e) {
            Log::warning('Falha ao enviar e-mail transacional: ' . $e->getMessage(), ['para' => $para]);

            return false;
        }
    }
}
