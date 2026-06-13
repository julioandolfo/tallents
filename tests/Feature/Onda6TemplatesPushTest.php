<?php

namespace Tests\Feature;

use App\Mail\OcorrenciaRegistrada;
use App\Models\Colaborador;
use App\Models\ConfiguracaoEmail;
use App\Models\ConfiguracaoPush;
use App\Models\Empresa;
use App\Models\Ocorrencia;
use App\Models\TemplateEmail;
use App\Models\TipoOcorrencia;
use App\Models\Usuario;
use App\Services\PushService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class Onda6TemplatesPushTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(Usuario::where('email', 'admin@tallents.com.br')->firstOrFail());
    }

    public function test_seeder_cria_templates_padrao(): void
    {
        $this->assertDatabaseHas('templates_email', ['chave' => 'boas_vindas']);
        $this->assertDatabaseHas('templates_email', ['chave' => 'ocorrencia']);
    }

    public function test_template_substitui_variaveis(): void
    {
        $tpl = TemplateEmail::paraChave('ocorrencia');
        $r = $tpl->render(['nome' => 'Ana', 'tipo' => 'Atraso', 'data' => '01/02/2024']);
        $this->assertStringContainsString('Ana', $r['corpo']);
        $this->assertStringContainsString('Atraso', $r['corpo']);
        $this->assertStringNotContainsString('{{', $r['corpo']);
    }

    public function test_editar_template(): void
    {
        $tpl = TemplateEmail::paraChave('ocorrencia');
        $this->put(route('configuracoes.templates.update', $tpl), [
            'assunto' => 'Novo assunto', 'corpo' => '<p>Olá {{ nome }}</p>', 'ativo' => '1',
        ])->assertRedirect(route('configuracoes.templates.index'));

        $this->assertEquals('Novo assunto', $tpl->fresh()->assunto);
    }

    public function test_mailable_usa_template_do_banco(): void
    {
        ConfiguracaoEmail::create(['driver' => 'smtp', 'smtp_host' => 'h', 'from_email' => 'r@x.com', 'smtp_port' => 587]);
        TemplateEmail::where('chave', 'ocorrencia')->update(['assunto' => 'ASSUNTO CUSTOMIZADO']);

        $emp = Empresa::firstOrFail();
        $colab = Colaborador::create(['empresa_id' => $emp->id, 'nome' => 'Ana', 'status' => 'ATIVO']);
        $tipo = TipoOcorrencia::create(['empresa_id' => $emp->id, 'nome' => 'Atraso', 'ativo' => true]);
        $oc = $colab->ocorrencias()->create(['empresa_id' => $emp->id, 'tipo_ocorrencia_id' => $tipo->id, 'registrado_por' => auth()->id(), 'data_ocorrencia' => '2024-02-01']);

        $mailable = new OcorrenciaRegistrada($oc->load(['colaborador', 'tipoOcorrencia']));
        $mailable->assertHasSubject('ASSUNTO CUSTOMIZADO');
    }

    public function test_push_salva_config_e_envia(): void
    {
        $this->put(route('configuracoes.push'), [
            'onesignal_app_id' => 'app-123', 'onesignal_api_key' => 'key-abc', 'ativo' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('configuracoes_push', ['onesignal_app_id' => 'app-123', 'ativo' => 1]);

        Http::fake(['onesignal.com/*' => Http::response(['id' => 'notif-1'], 200)]);
        $ok = app(PushService::class)->enviar([5], 'Título', 'Mensagem');
        $this->assertTrue($ok);
        Http::assertSent(fn($req) => str_contains($req->url(), 'onesignal.com'));
    }

    public function test_push_desativado_nao_envia(): void
    {
        ConfiguracaoPush::create(['onesignal_app_id' => 'a', 'onesignal_api_key' => 'k', 'ativo' => false]);
        Http::fake();
        $this->assertFalse(app(PushService::class)->enviar([5], 'T', 'M'));
        Http::assertNothingSent();
    }
}
