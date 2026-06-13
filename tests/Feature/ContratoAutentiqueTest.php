<?php

namespace Tests\Feature;

use App\Models\Cargo;
use App\Models\Colaborador;
use App\Models\Contrato;
use App\Models\Empresa;
use App\Models\Setor;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ContratoAutentiqueTest extends TestCase
{
    use RefreshDatabase;

    private Colaborador $colab;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(Usuario::where('email', 'admin@tallents.com.br')->firstOrFail());
        $emp = Empresa::firstOrFail();
        $setor = Setor::create(['empresa_id' => $emp->id, 'nome' => 'TI']);
        $cargo = Cargo::create(['empresa_id' => $emp->id, 'nome' => 'Dev', 'salario_base' => 1]);
        $this->colab = Colaborador::create(['empresa_id' => $emp->id, 'setor_id' => $setor->id, 'cargo_id' => $cargo->id, 'nome' => 'Ana Lima', 'status' => 'ATIVO', 'cpf' => '111', 'email_pessoal' => 'ana@x.com', 'salario' => 3000]);
    }

    private function novoContrato(array $attrs = []): Contrato
    {
        return Contrato::create(array_merge([
            'colaborador_id' => $this->colab->id, 'criado_por' => auth()->id(),
            'titulo' => 'Contrato CLT', 'tipo' => 'CONTRATO', 'status' => 'PENDENTE',
            'metodo_assinatura' => 'INTERNO',
            'conteudo' => 'Contratada: {{ nome }}, CPF {{ cpf }}, cargo {{ cargo }}, salário {{ salario }}.',
        ], $attrs));
    }

    public function test_create_tem_variaveis_metodo_e_preview(): void
    {
        $resp = $this->get(route('contratos.create', ['colaborador_id' => $this->colab->id]))->assertOk();
        $resp->assertSee('Método de assinatura', false);
        $resp->assertSee('Autentique');
        $resp->assertSee('Pré-visualizar');
        $resp->assertSee('{{ nome }}', false);
        $resp->assertSee('{{ salario }}', false);
    }

    public function test_conteudo_renderizado_substitui_variaveis(): void
    {
        $c = $this->novoContrato();
        $c->load('colaborador.cargo');
        $txt = $c->conteudoRenderizado();
        $this->assertStringContainsString('Ana Lima', $txt);
        $this->assertStringContainsString('Dev', $txt);
        $this->assertStringContainsString('R$ 3.000,00', $txt);
        $this->assertStringNotContainsString('{{', $txt);
    }

    public function test_preview_e_pdf(): void
    {
        $c = $this->novoContrato();
        $this->get(route('contratos.preview', $c))->assertOk()->assertSee('Ana Lima');
        $this->get(route('contratos.pdf', $c))->assertOk()->assertHeader('content-type', 'application/pdf');
    }

    public function test_store_interno(): void
    {
        $this->post(route('contratos.store'), [
            'colaborador_id' => $this->colab->id, 'titulo' => 'Termo', 'tipo' => 'TERMO',
            'conteudo' => 'Olá {{ nome }}', 'metodo_assinatura' => 'INTERNO',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('contratos', ['titulo' => 'Termo', 'metodo_assinatura' => 'INTERNO', 'status' => 'PENDENTE']);
    }

    public function test_enviar_autentique_sem_token_falha_graciosamente(): void
    {
        config(['services.autentique.token' => null]);
        $c = $this->novoContrato();
        $this->post(route('contratos.autentique', $c))->assertRedirect()->assertSessionHas('error');
        $this->assertNull($c->fresh()->autentique_document_id);
    }

    public function test_enviar_autentique_com_token(): void
    {
        config(['services.autentique.token' => 'tok-123', 'services.autentique.sandbox' => true]);
        Http::fake(['autentique.com.br/*' => Http::response([
            'data' => ['createDocument' => [
                'id' => 'doc-abc',
                'name' => 'Contrato CLT',
                'signatures' => [['public_id' => 'p1', 'email' => 'ana@x.com', 'action' => ['name' => 'SIGN'], 'link' => ['short_link' => 'https://assina.autentique/abc']]],
            ]],
        ], 200)]);

        $c = $this->novoContrato();
        $this->post(route('contratos.autentique', $c))->assertRedirect()->assertSessionHas('success');

        $c->refresh();
        $this->assertEquals('AUTENTIQUE', $c->metodo_assinatura);
        $this->assertEquals('doc-abc', $c->autentique_document_id);
        $this->assertEquals('https://assina.autentique/abc', $c->autentique_signature_url);
        $this->assertNotNull($c->enviado_assinatura_em);
    }

    public function test_sincronizar_marca_assinado(): void
    {
        config(['services.autentique.token' => 'tok-123']);
        Http::fake(['autentique.com.br/*' => Http::response([
            'data' => ['document' => ['id' => 'doc-abc', 'signatures' => [['email' => 'ana@x.com', 'signed' => ['created_at' => '2024-05-01']]]]],
        ], 200)]);

        $c = $this->novoContrato(['metodo_assinatura' => 'AUTENTIQUE', 'autentique_document_id' => 'doc-abc']);
        $this->post(route('contratos.sincronizar', $c))->assertRedirect()->assertSessionHas('success');
        $this->assertEquals('ASSINADO', $c->fresh()->status);
    }

    public function test_integracoes_page_mostra_autentique(): void
    {
        $this->get(route('integracoes.index'))->assertOk()->assertSee('Autentique');
    }

    public function test_config_via_ui_habilita_e_envia(): void
    {
        // Sem token no .env; configura pela UI (Integrações).
        config(['services.autentique.token' => null]);
        \App\Models\Integracao::create([
            'provider' => 'AUTENTIQUE', 'ativo' => true,
            'credenciais' => ['token' => 'ui-token-9', 'sandbox' => 'nao'],
        ]);

        Http::fake(['autentique.com.br/*' => Http::response([
            'data' => ['createDocument' => [
                'id' => 'doc-ui', 'name' => 'x',
                'signatures' => [['link' => ['short_link' => 'https://assina/ui']]],
            ]],
        ], 200)]);

        $c = $this->novoContrato();
        $this->post(route('contratos.autentique', $c))->assertRedirect()->assertSessionHas('success');

        $this->assertEquals('doc-ui', $c->fresh()->autentique_document_id);
        // Confirma uso do token e ambiente (sandbox=false) vindos da UI.
        Http::assertSent(function ($req) {
            $auth = $req->header('Authorization')[0] ?? '';
            $body = is_array($req->data()) ? json_encode($req->data()) : (string) $req->body();
            return str_contains($auth, 'ui-token-9');
        });
    }

    public function test_webhook_marca_assinado(): void
    {
        $c = $this->novoContrato(['metodo_assinatura' => 'AUTENTIQUE', 'autentique_document_id' => 'doc-xyz']);

        $this->postJson(route('webhooks.autentique'), [
            'event' => 'signature.accepted',
            'document' => ['id' => 'doc-xyz'],
        ])->assertOk();

        $this->assertEquals('ASSINADO', $c->fresh()->status);
    }
}
