<?php

namespace Tests\Feature;

use App\Models\Cargo;
use App\Models\Colaborador;
use App\Models\Demissao;
use App\Models\Empresa;
use App\Models\Setor;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemissaoTest extends TestCase
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

    public function test_registrar_desligamento_inativa_colaborador(): void
    {
        $this->post(route('demissoes.store'), [
            'colaborador_id'    => $this->colab->id,
            'tipo'              => 'SEM_JUSTA_CAUSA',
            'data_desligamento' => '2024-05-10',
            'aviso_previo'      => 'INDENIZADO',
        ])->assertRedirect();

        $this->assertDatabaseCount('demissoes', 1);

        $colab = $this->colab->fresh();
        $this->assertEquals('INATIVO', $colab->status);
        $this->assertEquals('2024-05-10', $colab->data_demissao->format('Y-m-d'));
    }

    public function test_reverter_desligamento_reativa_colaborador(): void
    {
        $this->colab->update(['status' => 'INATIVO', 'data_demissao' => '2024-05-10']);
        $dem = Demissao::create([
            'colaborador_id'    => $this->colab->id,
            'empresa_id'        => $this->colab->empresa_id,
            'tipo'              => 'PEDIDO',
            'data_desligamento' => '2024-05-10',
        ]);

        $this->delete(route('demissoes.destroy', $dem))->assertRedirect();

        $this->assertDatabaseMissing('demissoes', ['id' => $dem->id]);
        $colab = $this->colab->fresh();
        $this->assertEquals('ATIVO', $colab->status);
        $this->assertNull($colab->data_demissao);
    }

    public function test_paginas_renderizam(): void
    {
        $this->get(route('demissoes.index'))->assertOk();
        $this->get(route('demissoes.create'))->assertOk();
    }
}
