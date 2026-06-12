<?php

namespace Tests\Feature;

use App\Models\Avaliacao;
use App\Models\Cargo;
use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\Feedback;
use App\Models\Pdi;
use App\Models\Setor;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DesempenhoTest extends TestCase
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
        $this->colab = Colaborador::create(['empresa_id' => $emp->id, 'setor_id' => $setor->id, 'cargo_id' => $cargo->id, 'nome' => 'Ana', 'status' => 'ATIVO']);
    }

    public function test_criar_avaliacao(): void
    {
        $this->post(route('avaliacoes.store'), [
            'colaborador_id' => $this->colab->id, 'ciclo' => '2024-S1', 'tipo' => 'GESTOR',
            'nota_geral' => 8.5, 'status' => 'CONCLUIDA', 'data' => '2024-06-01',
        ])->assertRedirect(route('avaliacoes.index'));

        $this->assertDatabaseHas('avaliacoes', ['colaborador_id' => $this->colab->id, 'ciclo' => '2024-S1']);
    }

    public function test_criar_feedback(): void
    {
        $this->post(route('feedbacks.store'), [
            'colaborador_id' => $this->colab->id, 'tipo' => 'ELOGIO', 'mensagem' => 'Ótimo trabalho!', 'visivel_colaborador' => '1',
        ])->assertRedirect(route('feedbacks.index'));

        $fb = Feedback::firstOrFail();
        $this->assertEquals('ELOGIO', $fb->tipo);
        $this->assertTrue($fb->visivel_colaborador);
    }

    public function test_pdi_acoes_concluem_plano(): void
    {
        $pdi = Pdi::create(['colaborador_id' => $this->colab->id, 'titulo' => 'Liderança', 'status' => 'EM_ANDAMENTO']);

        $this->post(route('pdis.acoes.store', $pdi), ['descricao' => 'Curso de liderança'])->assertRedirect();
        $acao = $pdi->acoes()->firstOrFail();

        $this->patch(route('pdis.acoes.toggle', $acao))->assertRedirect();
        $this->assertTrue($acao->fresh()->concluida);
        $this->assertEquals('CONCLUIDO', $pdi->fresh()->status);
        $this->assertEquals(100, $pdi->fresh()->progresso());
    }

    public function test_paginas_renderizam(): void
    {
        $av = Avaliacao::create(['colaborador_id' => $this->colab->id, 'ciclo' => '2024-S1', 'tipo' => 'AUTO', 'status' => 'PENDENTE']);
        $pdi = Pdi::create(['colaborador_id' => $this->colab->id, 'titulo' => 'X', 'status' => 'EM_ANDAMENTO']);

        $this->get(route('avaliacoes.index'))->assertOk();
        $this->get(route('avaliacoes.create'))->assertOk();
        $this->get(route('avaliacoes.show', $av))->assertOk();
        $this->get(route('feedbacks.index'))->assertOk();
        $this->get(route('feedbacks.create'))->assertOk();
        $this->get(route('pdis.index'))->assertOk();
        $this->get(route('pdis.create'))->assertOk();
        $this->get(route('pdis.show', $pdi))->assertOk();
    }
}
