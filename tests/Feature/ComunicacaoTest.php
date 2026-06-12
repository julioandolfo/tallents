<?php

namespace Tests\Feature;

use App\Models\Comunicado;
use App\Models\Empresa;
use App\Models\Evento;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComunicacaoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(Usuario::where('email', 'admin@tallents.com.br')->firstOrFail());
    }

    public function test_criar_comunicado(): void
    {
        $this->post(route('comunicados.store'), [
            'titulo' => 'Aviso', 'conteudo' => 'Conteúdo do aviso', 'categoria' => 'RH',
            'destaque' => '1', 'publicado' => '1',
        ])->assertRedirect(route('comunicados.index'));

        $com = Comunicado::firstOrFail();
        $this->assertEquals('Aviso', $com->titulo);
        $this->assertTrue($com->destaque);
        $this->assertTrue($com->publicado);
        $this->assertNotNull($com->publicado_em);
    }

    public function test_criar_evento_e_documento(): void
    {
        $this->post(route('eventos.store'), [
            'titulo' => 'Reunião geral', 'tipo' => 'REUNIAO', 'inicio' => now()->addDay()->format('Y-m-d H:i'),
        ])->assertRedirect(route('eventos.index'));
        $this->assertDatabaseHas('eventos', ['titulo' => 'Reunião geral']);

        $this->post(route('documentos.store'), [
            'titulo' => 'Manual de conduta', 'categoria' => 'MANUAL', 'url' => 'https://exemplo.com/manual.pdf',
        ])->assertRedirect(route('documentos.index'));
        $this->assertDatabaseHas('documentos', ['titulo' => 'Manual de conduta']);
    }

    public function test_paginas_admin_renderizam(): void
    {
        $this->get(route('comunicados.index'))->assertOk();
        $this->get(route('comunicados.create'))->assertOk();
        $this->get(route('eventos.index'))->assertOk();
        $this->get(route('eventos.create'))->assertOk();
        $this->get(route('documentos.index'))->assertOk();
        $this->get(route('documentos.create'))->assertOk();
    }

    public function test_mural_do_portal_mostra_comunicados_publicados(): void
    {
        $emp = Empresa::firstOrFail();
        Comunicado::create(['titulo' => 'Publicado', 'conteudo' => 'x', 'categoria' => 'GERAL', 'publicado' => true, 'publicado_em' => now()]);
        Comunicado::create(['titulo' => 'Rascunho', 'conteudo' => 'y', 'categoria' => 'GERAL', 'publicado' => false]);
        Evento::create(['titulo' => 'Futuro', 'tipo' => 'REUNIAO', 'inicio' => now()->addDays(2)]);

        $this->get(route('portal.mural'))
            ->assertOk()
            ->assertSee('Publicado')
            ->assertDontSee('Rascunho')
            ->assertSee('Futuro');
    }
}
