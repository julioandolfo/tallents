<?php

namespace App\Mail;

use App\Models\TemplateEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Base para e-mails que podem usar um template editável (tabela templates_email).
 * Se houver template ativo para a chave, usa-o; senão, cai no blade padrão.
 */
abstract class TemplateMailable extends Mailable
{
    use Queueable, SerializesModels;

    protected function comTemplate(string $chave, array $dados, string $viewFallback, string $assuntoFallback): self
    {
        $tpl = TemplateEmail::paraChave($chave);

        if ($tpl) {
            $r = $tpl->render($dados);

            return $this->subject($r['assunto'])
                ->view('emails.template', ['titulo' => $r['assunto'], 'corpoHtml' => $r['corpo']]);
        }

        return $this->subject($assuntoFallback)->view($viewFallback);
    }
}
