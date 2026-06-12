<?php

namespace Tests\Feature;

use App\Models\Integracao;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IntegracaoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(Usuario::where('email', 'admin@tallents.com.br')->firstOrFail());
    }

    public function test_pagina_renderiza_providers(): void
    {
        $this->get(route('integracoes.index'))
            ->assertOk()
            ->assertSee('WhatsApp')
            ->assertSee('Slack')
            ->assertSee('Inteligência Artificial');
    }

    public function test_salvar_credenciais(): void
    {
        $this->put(route('integracoes.update', 'SLACK'), [
            'ativo' => '1',
            'credenciais' => ['webhook_url' => 'https://hooks.slack.com/x', 'canal' => '#rh'],
        ])->assertRedirect();

        $integ = Integracao::where('provider', 'SLACK')->firstOrFail();
        $this->assertTrue($integ->ativo);
        $this->assertEquals('#rh', $integ->valor('canal'));
    }

    public function test_provider_invalido_404(): void
    {
        $this->put(route('integracoes.update', 'INEXISTENTE'), ['ativo' => '1'])->assertNotFound();
    }
}
