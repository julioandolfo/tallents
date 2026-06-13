<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormulariosLayoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(Usuario::where('email', 'admin@tallents.com.br')->firstOrFail());
    }

    public function test_formularios_usam_largura_total_e_grid(): void
    {
        $resp = $this->get(route('cargos.create'))->assertOk();
        // Full-width: nao deve ter mais o cap estreito; deve usar a classe de grid.
        $resp->assertSee('p-6 form-grid', false);
        $resp->assertDontSee('py-4 max-w-xl', false);
    }

    public function test_paginas_de_formulario_renderizam(): void
    {
        foreach (['cargos.create', 'setores.create', 'niveis-hierarquicos.create', 'tipos-bonus.create', 'tipos-ocorrencias.create', 'promocoes.create'] as $rota) {
            $this->get(route($rota))->assertOk();
        }
    }
}
