<?php

namespace Tests\Feature;

use App\Models\Cargo;
use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\Ocorrencia;
use App\Models\Setor;
use App\Models\TipoOcorrencia;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdvertenciaTest extends TestCase
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

    public function test_cria_advertencia(): void
    {
        $this->post(route('advertencias.store'), [
            'colaborador_id' => $this->colab->id,
            'tipo' => 'ESCRITA',
            'motivo' => 'Atrasos recorrentes',
            'data' => '2024-03-01',
        ])->assertRedirect();

        $this->assertDatabaseHas('advertencias', [
            'colaborador_id' => $this->colab->id, 'tipo' => 'ESCRITA', 'empresa_id' => $this->colab->empresa_id,
        ]);
    }

    public function test_advertencia_a_partir_de_ocorrencia_registra_historico(): void
    {
        $emp = Empresa::firstOrFail();
        $tipo = TipoOcorrencia::create(['empresa_id' => $emp->id, 'nome' => 'Atraso']);
        $oc = Ocorrencia::create(['empresa_id' => $emp->id, 'colaborador_id' => $this->colab->id, 'tipo_ocorrencia_id' => $tipo->id, 'data_ocorrencia' => '2024-01-01']);

        $this->post(route('advertencias.store'), [
            'colaborador_id' => $this->colab->id,
            'ocorrencia_id' => $oc->id,
            'tipo' => 'SUSPENSAO',
            'dias_suspensao' => 3,
            'motivo' => 'Reincidência',
            'data' => '2024-03-02',
        ])->assertRedirect();

        $this->assertDatabaseHas('advertencias', ['ocorrencia_id' => $oc->id, 'tipo' => 'SUSPENSAO', 'dias_suspensao' => 3]);
        $this->assertDatabaseHas('ocorrencias_historico', ['ocorrencia_id' => $oc->id, 'acao' => 'Advertência gerada']);
    }

    public function test_paginas_renderizam(): void
    {
        \App\Models\Advertencia::create(['empresa_id' => $this->colab->empresa_id, 'colaborador_id' => $this->colab->id, 'tipo' => 'VERBAL', 'motivo' => 'x', 'data' => '2024-01-01']);
        $adv = \App\Models\Advertencia::first();

        $this->get(route('advertencias.index'))->assertOk();
        $this->get(route('advertencias.create'))->assertOk();
        $this->get(route('advertencias.show', $adv))->assertOk();
    }
}
