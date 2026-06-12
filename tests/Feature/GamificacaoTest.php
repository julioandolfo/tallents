<?php

namespace Tests\Feature;

use App\Models\Cargo;
use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\Recompensa;
use App\Models\Resgate;
use App\Models\Setor;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GamificacaoTest extends TestCase
{
    use RefreshDatabase;

    private Colaborador $colab;
    private Usuario $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = Usuario::where('email', 'admin@tallents.com.br')->firstOrFail();

        $emp = Empresa::firstOrFail();
        $setor = Setor::create(['empresa_id' => $emp->id, 'nome' => 'TI']);
        $cargo = Cargo::create(['empresa_id' => $emp->id, 'nome' => 'Dev', 'salario_base' => 1]);
        $this->colab = Colaborador::create([
            'empresa_id' => $emp->id, 'setor_id' => $setor->id, 'cargo_id' => $cargo->id,
            'nome' => 'Ana', 'status' => 'ATIVO', 'email_login' => 'ana@tallents.com.br',
        ]);
    }

    public function test_admin_lanca_pontos_e_saldo_atualiza(): void
    {
        $this->actingAs($this->admin);
        $this->post(route('pontos.lancar', $this->colab), ['pontos' => 100, 'origem' => 'ELOGIO', 'descricao' => 'Bom trabalho'])->assertRedirect();
        $this->post(route('pontos.lancar', $this->colab), ['pontos' => -30, 'origem' => 'AJUSTE'])->assertRedirect();

        $this->assertEquals(70, $this->colab->fresh()->saldoPontos());
    }

    public function test_colaborador_resgata_recompensa_debita_pontos(): void
    {
        $this->colab->pontosMovimentacoes()->create(['origem' => 'MANUAL', 'pontos' => 200]);
        $rec = Recompensa::create(['nome' => 'Vale-café', 'custo_pontos' => 50, 'estoque' => 5, 'ativa' => true]);

        $user = Usuario::create([
            'name' => 'Ana', 'email' => 'ana@tallents.com.br', 'password' => bcrypt('x'),
            'role' => 'COLABORADOR', 'colaborador_id' => $this->colab->id,
        ]);
        $this->actingAs($user);

        $this->post(route('portal.loja.resgatar', $rec))->assertRedirect();

        $this->assertEquals(150, $this->colab->fresh()->saldoPontos());
        $this->assertDatabaseHas('resgates', ['colaborador_id' => $this->colab->id, 'status' => 'PENDENTE']);
        $this->assertEquals(4, $rec->fresh()->estoque);
    }

    public function test_resgate_sem_pontos_suficientes_e_bloqueado(): void
    {
        $this->colab->pontosMovimentacoes()->create(['origem' => 'MANUAL', 'pontos' => 10]);
        $rec = Recompensa::create(['nome' => 'Caro', 'custo_pontos' => 500, 'ativa' => true]);

        $user = Usuario::create([
            'name' => 'Ana', 'email' => 'ana@tallents.com.br', 'password' => bcrypt('x'),
            'role' => 'COLABORADOR', 'colaborador_id' => $this->colab->id,
        ]);
        $this->actingAs($user);

        $this->post(route('portal.loja.resgatar', $rec));

        $this->assertEquals(10, $this->colab->fresh()->saldoPontos());
        $this->assertDatabaseCount('resgates', 0);
    }

    public function test_cancelar_resgate_estorna_pontos(): void
    {
        $this->colab->pontosMovimentacoes()->create(['origem' => 'MANUAL', 'pontos' => 100]);
        $rec = Recompensa::create(['nome' => 'X', 'custo_pontos' => 40, 'estoque' => 2, 'ativa' => true]);
        $resgate = Resgate::create(['colaborador_id' => $this->colab->id, 'recompensa_id' => $rec->id, 'custo_pontos' => 40, 'status' => 'PENDENTE']);
        $this->colab->pontosMovimentacoes()->create(['origem' => 'RESGATE', 'pontos' => -40]);
        $this->assertEquals(60, $this->colab->fresh()->saldoPontos());

        $this->actingAs($this->admin);
        $this->patch(route('resgates.update', $resgate), ['status' => 'CANCELADO'])->assertRedirect();

        $this->assertEquals(100, $this->colab->fresh()->saldoPontos());
        $this->assertEquals(3, $rec->fresh()->estoque);
    }

    public function test_paginas_admin_renderizam(): void
    {
        $this->actingAs($this->admin);
        $this->get(route('pontos.index'))->assertOk();
        $this->get(route('pontos.extrato', $this->colab))->assertOk();
        $this->get(route('recompensas.index'))->assertOk();
        $this->get(route('recompensas.create'))->assertOk();
        $this->get(route('resgates.index'))->assertOk();
    }
}
