<?php

namespace Tests\Feature;

use App\Models\Cargo;
use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\Onboarding;
use App\Models\Setor;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnboardingTest extends TestCase
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

    public function test_criar_onboarding_gera_checklist_padrao(): void
    {
        $this->post(route('onboarding.store'), [
            'colaborador_id' => $this->colab->id,
            'data_inicio'    => '2024-01-02',
            'usar_padrao'    => '1',
        ])->assertRedirect();

        $this->assertDatabaseCount('onboardings', 1);
        $this->assertDatabaseCount('onboarding_tarefas', count(Onboarding::TAREFAS_PADRAO));
    }

    public function test_concluir_todas_tarefas_conclui_onboarding(): void
    {
        $ob = Onboarding::create([
            'colaborador_id' => $this->colab->id,
            'empresa_id'     => $this->colab->empresa_id,
            'data_inicio'    => '2024-01-02',
            'status'         => 'EM_ANDAMENTO',
        ]);
        $t1 = $ob->tarefas()->create(['titulo' => 'A']);
        $t2 = $ob->tarefas()->create(['titulo' => 'B']);

        $this->patch(route('onboarding.tarefas.toggle', $t1))->assertRedirect();
        $this->assertEquals('EM_ANDAMENTO', $ob->fresh()->status);

        $this->patch(route('onboarding.tarefas.toggle', $t2))->assertRedirect();
        $this->assertTrue($t2->fresh()->concluida);
        $this->assertEquals('CONCLUIDO', $ob->fresh()->status);
        $this->assertEquals(100, $ob->fresh()->progresso());
    }

    public function test_adicionar_e_remover_tarefa(): void
    {
        $ob = Onboarding::create([
            'colaborador_id' => $this->colab->id,
            'empresa_id'     => $this->colab->empresa_id,
            'data_inicio'    => '2024-01-02',
            'status'         => 'EM_ANDAMENTO',
        ]);

        $this->post(route('onboarding.tarefas.store', $ob), ['titulo' => 'Nova tarefa'])->assertRedirect();
        $this->assertDatabaseHas('onboarding_tarefas', ['onboarding_id' => $ob->id, 'titulo' => 'Nova tarefa']);

        $tarefa = $ob->tarefas()->first();
        $this->delete(route('onboarding.tarefas.destroy', $tarefa))->assertRedirect();
        $this->assertDatabaseMissing('onboarding_tarefas', ['id' => $tarefa->id]);
    }

    public function test_paginas_renderizam(): void
    {
        $this->get(route('onboarding.index'))->assertOk();
        $this->get(route('onboarding.create'))->assertOk();
    }
}
