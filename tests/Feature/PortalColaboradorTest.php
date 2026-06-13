<?php

namespace Tests\Feature;

use App\Models\Colaborador;
use App\Models\Contrato;
use App\Models\Empresa;
use App\Models\Recompensa;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalColaboradorTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $emp;
    private Colaborador $colab;
    private Usuario $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->emp = Empresa::firstOrFail();
        $this->colab = Colaborador::create(['empresa_id' => $this->emp->id, 'nome' => 'Ana', 'status' => 'ATIVO', 'email_login' => 'ana@x.com']);
        $this->user = Usuario::create(['name' => 'Ana', 'email' => 'ana@x.com', 'password' => bcrypt('x'), 'role' => 'COLABORADOR', 'empresa_id' => $this->emp->id, 'colaborador_id' => $this->colab->id, 'ativo' => true]);
        $this->actingAs($this->user);
    }

    public function test_paginas_do_portal_renderizam(): void
    {
        $this->get(route('portal.index'))->assertOk();
        $this->get(route('portal.mural'))->assertOk();
        $this->get(route('portal.loja'))->assertOk();
        $this->get(route('portal.contratos.index'))->assertOk();
    }

    public function test_colaborador_assina_proprio_contrato(): void
    {
        $contrato = Contrato::create(['colaborador_id' => $this->colab->id, 'criado_por' => $this->user->id, 'titulo' => 'CLT', 'tipo' => 'CONTRATO', 'status' => 'PENDENTE']);

        $this->get(route('portal.contratos.show', $contrato))->assertOk();

        $this->post(route('portal.contratos.assinar', $contrato), ['aceite' => '1'])
            ->assertRedirect(route('portal.contratos.show', $contrato))
            ->assertSessionHasNoErrors();

        $contrato->refresh();
        $this->assertEquals('ASSINADO', $contrato->status);
        $this->assertNotNull($contrato->assinado_em);
        $this->assertNotNull($contrato->assinatura_ip);
    }

    public function test_assinatura_exige_aceite(): void
    {
        $contrato = Contrato::create(['colaborador_id' => $this->colab->id, 'criado_por' => $this->user->id, 'titulo' => 'CLT', 'tipo' => 'CONTRATO', 'status' => 'PENDENTE']);

        $this->post(route('portal.contratos.assinar', $contrato), [])->assertSessionHasErrors('aceite');
        $this->assertEquals('PENDENTE', $contrato->fresh()->status);
    }

    public function test_nao_acessa_contrato_de_outro_colaborador(): void
    {
        $outro = Colaborador::create(['empresa_id' => $this->emp->id, 'nome' => 'Bruno', 'status' => 'ATIVO']);
        $contrato = Contrato::create(['colaborador_id' => $outro->id, 'criado_por' => $this->user->id, 'titulo' => 'X', 'tipo' => 'CONTRATO', 'status' => 'PENDENTE']);

        $this->get(route('portal.contratos.show', $contrato))->assertForbidden();
        $this->post(route('portal.contratos.assinar', $contrato), ['aceite' => '1'])->assertForbidden();
    }

    public function test_resgata_recompensa_com_saldo_e_debita_pontos(): void
    {
        $this->colab->pontosMovimentacoes()->create(['origem' => 'AJUSTE', 'pontos' => 100, 'descricao' => 'Bônus inicial']);
        $recompensa = Recompensa::create(['nome' => 'Vale-café', 'custo_pontos' => 30, 'estoque' => 5, 'ativa' => true]);

        $this->post(route('portal.loja.resgatar', $recompensa), [])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('resgates', ['colaborador_id' => $this->colab->id, 'recompensa_id' => $recompensa->id, 'status' => 'PENDENTE']);
        $this->assertEquals(70, $this->colab->fresh()->saldoPontos()); // 100 - 30
        $this->assertEquals(4, $recompensa->fresh()->estoque);
    }

    public function test_resgate_bloqueado_sem_saldo(): void
    {
        $this->colab->pontosMovimentacoes()->create(['origem' => 'AJUSTE', 'pontos' => 10, 'descricao' => 'x']);
        $recompensa = Recompensa::create(['nome' => 'Caro', 'custo_pontos' => 500, 'estoque' => 5, 'ativa' => true]);

        $this->post(route('portal.loja.resgatar', $recompensa), [])->assertSessionHasErrors('recompensa');
        $this->assertEquals(10, $this->colab->fresh()->saldoPontos());
        $this->assertDatabaseMissing('resgates', ['colaborador_id' => $this->colab->id]);
    }

    public function test_usuario_sem_colaborador_recebe_403_na_loja(): void
    {
        Usuario::create(['name' => 'Solto', 'email' => 'solto@x.com', 'password' => bcrypt('x'), 'role' => 'COLABORADOR', 'empresa_id' => $this->emp->id, 'ativo' => true]);
        // Recarrega do banco (como no login real: todas as colunas hidratadas).
        $this->actingAs(Usuario::where('email', 'solto@x.com')->firstOrFail());

        $this->get(route('portal.loja'))->assertForbidden();
        $this->get(route('portal.contratos.index'))->assertForbidden();
    }
}
