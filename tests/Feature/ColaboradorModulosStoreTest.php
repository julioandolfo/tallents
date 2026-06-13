<?php

namespace Tests\Feature;

use App\Models\Cargo;
use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\Setor;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Valida o fluxo inverso: abrir os formulários "Novo …" a partir da ficha
 * (com colaborador_id) e salvar (store), confirmando persistência e redirect.
 */
class ColaboradorModulosStoreTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $emp;
    private Colaborador $colab;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(Usuario::where('email', 'admin@tallents.com.br')->firstOrFail());
        $this->emp = Empresa::firstOrFail();
        $setor = Setor::create(['empresa_id' => $this->emp->id, 'nome' => 'TI']);
        $cargo = Cargo::create(['empresa_id' => $this->emp->id, 'nome' => 'Dev', 'salario_base' => 1]);
        $this->colab = Colaborador::create(['empresa_id' => $this->emp->id, 'setor_id' => $setor->id, 'cargo_id' => $cargo->id, 'nome' => 'Ana', 'status' => 'ATIVO']);
    }

    public function test_paginas_create_abrem_a_partir_da_ficha(): void
    {
        foreach (['advertencias.create', 'avaliacoes.create', 'pdis.create', 'onboarding.create', 'feedbacks.create', 'contratos.create'] as $rota) {
            $this->get(route($rota, ['colaborador_id' => $this->colab->id]))
                ->assertOk();
        }
    }

    public function test_store_advertencia(): void
    {
        $this->post(route('advertencias.store'), [
            'colaborador_id' => $this->colab->id, 'tipo' => 'VERBAL', 'motivo' => 'Atraso', 'data' => '2024-03-01',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('advertencias', ['colaborador_id' => $this->colab->id, 'tipo' => 'VERBAL', 'aplicada_por' => auth()->id()]);
    }

    public function test_store_avaliacao(): void
    {
        $this->post(route('avaliacoes.store'), [
            'colaborador_id' => $this->colab->id, 'ciclo' => '2024.1', 'tipo' => 'GESTOR', 'status' => 'PENDENTE', 'nota_geral' => 7.5,
        ])->assertRedirect(route('avaliacoes.index'))->assertSessionHasNoErrors();

        $this->assertDatabaseHas('avaliacoes', ['colaborador_id' => $this->colab->id, 'ciclo' => '2024.1', 'avaliador_id' => auth()->id()]);
    }

    public function test_store_pdi(): void
    {
        $this->post(route('pdis.store'), [
            'colaborador_id' => $this->colab->id, 'titulo' => 'Liderança', 'prazo' => '2024-12-31',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('pdis', ['colaborador_id' => $this->colab->id, 'titulo' => 'Liderança', 'status' => 'EM_ANDAMENTO']);
    }

    public function test_store_onboarding_com_checklist_padrao(): void
    {
        $this->post(route('onboarding.store'), [
            'colaborador_id' => $this->colab->id, 'data_inicio' => '2024-01-05',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('onboardings', ['colaborador_id' => $this->colab->id, 'status' => 'EM_ANDAMENTO']);
        // Checklist padrão criado.
        $this->assertDatabaseCount('onboarding_tarefas', count(\App\Models\Onboarding::TAREFAS_PADRAO));
    }

    public function test_store_feedback(): void
    {
        $this->post(route('feedbacks.store'), [
            'colaborador_id' => $this->colab->id, 'tipo' => 'ELOGIO', 'mensagem' => 'Ótimo trabalho', 'visivel_colaborador' => '1',
        ])->assertRedirect(route('feedbacks.index'))->assertSessionHasNoErrors();

        $this->assertDatabaseHas('feedbacks', ['colaborador_id' => $this->colab->id, 'tipo' => 'ELOGIO', 'autor_id' => auth()->id()]);
    }

    public function test_store_contrato(): void
    {
        $this->post(route('contratos.store'), [
            'colaborador_id' => $this->colab->id, 'titulo' => 'Contrato CLT', 'tipo' => 'CONTRATO', 'conteudo' => 'Cláusulas...',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('contratos', ['colaborador_id' => $this->colab->id, 'titulo' => 'Contrato CLT', 'status' => 'PENDENTE']);
    }

    public function test_lancar_banco_de_horas_atualiza_saldo(): void
    {
        $this->post(route('banco-horas.lancar', $this->colab), [
            'tipo' => 'CREDITO', 'horas' => 5, 'motivo' => 'Hora extra', 'data' => '2024-03-01',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->post(route('banco-horas.lancar', $this->colab), [
            'tipo' => 'DEBITO', 'horas' => 2, 'motivo' => 'Folga', 'data' => '2024-03-05',
        ])->assertRedirect();

        $this->assertEquals(3.0, $this->colab->fresh()->saldoBancoHoras());
    }

    public function test_validacao_bloqueia_store_invalido(): void
    {
        // tipo de advertência inválido
        $this->post(route('advertencias.store'), [
            'colaborador_id' => $this->colab->id, 'tipo' => 'INVALIDO', 'motivo' => 'x', 'data' => '2024-03-01',
        ])->assertSessionHasErrors('tipo');

        // feedback sem mensagem
        $this->post(route('feedbacks.store'), [
            'colaborador_id' => $this->colab->id, 'tipo' => 'ELOGIO',
        ])->assertSessionHasErrors('mensagem');
    }
}
