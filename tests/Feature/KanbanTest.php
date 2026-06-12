<?php

namespace Tests\Feature;

use App\Models\Quadro;
use App\Models\Tarefa;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KanbanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(Usuario::where('email', 'admin@tallents.com.br')->firstOrFail());
    }

    public function test_criar_quadro_e_tarefa(): void
    {
        $this->post(route('quadros.store'), ['nome' => 'Projeto X'])->assertRedirect();
        $quadro = Quadro::firstOrFail();

        $this->post(route('tarefas.store', $quadro), ['titulo' => 'Primeira tarefa', 'prioridade' => 'ALTA'])->assertRedirect();
        $this->assertDatabaseHas('tarefas', ['quadro_id' => $quadro->id, 'titulo' => 'Primeira tarefa', 'status' => 'A_FAZER']);
    }

    public function test_mover_tarefa_entre_colunas(): void
    {
        $quadro = Quadro::create(['nome' => 'Q']);
        $tarefa = $quadro->tarefas()->create(['titulo' => 'T', 'status' => 'A_FAZER']);

        $this->patch(route('tarefas.mover', $tarefa), ['status' => 'FAZENDO'])->assertRedirect();
        $this->assertEquals('FAZENDO', $tarefa->fresh()->status);
    }

    public function test_remover_tarefa(): void
    {
        $quadro = Quadro::create(['nome' => 'Q']);
        $tarefa = $quadro->tarefas()->create(['titulo' => 'T']);

        $this->delete(route('tarefas.destroy', $tarefa))->assertRedirect(route('quadros.show', $quadro));
        $this->assertDatabaseMissing('tarefas', ['id' => $tarefa->id]);
    }

    public function test_paginas_renderizam(): void
    {
        $quadro = Quadro::create(['nome' => 'Q']);
        $quadro->tarefas()->create(['titulo' => 'Tarefa visível', 'status' => 'FAZENDO']);

        $this->get(route('quadros.index'))->assertOk();
        $this->get(route('quadros.show', $quadro))->assertOk()->assertSee('Tarefa visível');
    }
}
