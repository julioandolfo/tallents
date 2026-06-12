<?php

namespace Tests\Feature;

use App\Models\Cargo;
use App\Models\Colaborador;
use App\Models\Contrato;
use App\Models\Empresa;
use App\Models\Setor;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContratoTest extends TestCase
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
        $this->colab = Colaborador::create(['empresa_id' => $emp->id, 'setor_id' => $setor->id, 'cargo_id' => $cargo->id, 'nome' => 'Ana', 'status' => 'ATIVO']);
    }

    private function colaboradorUser(): Usuario
    {
        return Usuario::create([
            'name' => 'Ana', 'email' => 'ana@tallents.com.br', 'password' => bcrypt('x'),
            'role' => 'COLABORADOR', 'colaborador_id' => $this->colab->id,
        ]);
    }

    public function test_admin_cria_contrato(): void
    {
        $this->actingAs($this->admin);
        $this->post(route('contratos.store'), [
            'colaborador_id' => $this->colab->id, 'titulo' => 'Contrato CLT', 'tipo' => 'CONTRATO', 'conteudo' => 'Termos...',
        ])->assertRedirect();

        $this->assertDatabaseHas('contratos', ['titulo' => 'Contrato CLT', 'status' => 'PENDENTE']);
    }

    public function test_colaborador_assina_contrato(): void
    {
        $contrato = Contrato::create(['colaborador_id' => $this->colab->id, 'titulo' => 'X', 'tipo' => 'CONTRATO', 'conteudo' => 'y', 'status' => 'PENDENTE']);

        $this->actingAs($this->colaboradorUser());
        $this->post(route('portal.contratos.assinar', $contrato), ['aceite' => '1'])->assertRedirect();

        $contrato->refresh();
        $this->assertEquals('ASSINADO', $contrato->status);
        $this->assertNotNull($contrato->assinado_em);
        $this->assertNotNull($contrato->assinatura_ip);
    }

    public function test_colaborador_nao_assina_sem_aceite(): void
    {
        $contrato = Contrato::create(['colaborador_id' => $this->colab->id, 'titulo' => 'X', 'tipo' => 'CONTRATO', 'status' => 'PENDENTE']);

        $this->actingAs($this->colaboradorUser());
        $this->post(route('portal.contratos.assinar', $contrato), [])->assertSessionHasErrors('aceite');

        $this->assertEquals('PENDENTE', $contrato->fresh()->status);
    }

    public function test_colaborador_nao_acessa_contrato_de_outro(): void
    {
        $outro = Colaborador::create(['empresa_id' => $this->colab->empresa_id, 'nome' => 'Outro', 'status' => 'ATIVO']);
        $contrato = Contrato::create(['colaborador_id' => $outro->id, 'titulo' => 'Z', 'tipo' => 'TERMO', 'status' => 'PENDENTE']);

        $this->actingAs($this->colaboradorUser());
        $this->get(route('portal.contratos.show', $contrato))->assertForbidden();
    }

    public function test_paginas_renderizam(): void
    {
        $this->actingAs($this->admin);
        $this->get(route('contratos.index'))->assertOk();
        $this->get(route('contratos.create'))->assertOk();
    }
}
