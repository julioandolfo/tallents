<?php

namespace Tests\Feature;

use App\Models\Cargo;
use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\Setor;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BancoHorasTest extends TestCase
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

    public function test_lancamentos_e_saldo(): void
    {
        $this->post(route('banco-horas.lancar', $this->colab), ['tipo' => 'CREDITO', 'horas' => 8, 'data' => '2024-01-01'])->assertRedirect();
        $this->post(route('banco-horas.lancar', $this->colab), ['tipo' => 'DEBITO', 'horas' => 3, 'data' => '2024-01-02'])->assertRedirect();

        $this->assertEquals(5.0, $this->colab->fresh()->saldoBancoHoras());
        $this->assertDatabaseCount('banco_horas_movimentacoes', 2);
    }

    public function test_remover_movimentacao(): void
    {
        $mov = $this->colab->movimentacoesBanco()->create(['empresa_id' => $this->colab->empresa_id, 'tipo' => 'CREDITO', 'horas' => 4, 'data' => '2024-01-01']);
        $this->delete(route('banco-horas.movimentacoes.destroy', $mov))->assertRedirect();
        $this->assertDatabaseMissing('banco_horas_movimentacoes', ['id' => $mov->id]);
    }

    public function test_paginas_renderizam(): void
    {
        $this->get(route('banco-horas.index'))->assertOk();
        $this->get(route('banco-horas.extrato', $this->colab))->assertOk();
    }
}
