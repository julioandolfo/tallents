<?php

namespace Tests\Feature;

use App\Models\Cargo;
use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\Setor;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Onda3AjaxTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $emp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(Usuario::where('email', 'admin@tallents.com.br')->firstOrFail());
        $this->emp = Empresa::firstOrFail();
    }

    public function test_endpoint_setores_por_empresa(): void
    {
        Setor::create(['empresa_id' => $this->emp->id, 'nome' => 'TI', 'ativo' => true]);
        $outra = Empresa::create(['nome' => 'Outra', 'ativa' => true]);
        Setor::create(['empresa_id' => $outra->id, 'nome' => 'RH', 'ativo' => true]);

        $resp = $this->getJson(route('api.setores', ['empresa_id' => $this->emp->id]))->assertOk()->json();
        $nomes = collect($resp)->pluck('nome');
        $this->assertTrue($nomes->contains('TI'));
        $this->assertFalse($nomes->contains('RH'));
    }

    public function test_endpoint_cargos_por_empresa(): void
    {
        Cargo::create(['empresa_id' => $this->emp->id, 'nome' => 'Dev', 'salario_base' => 1, 'ativo' => true]);
        $this->getJson(route('api.cargos', ['empresa_id' => $this->emp->id]))->assertOk()->assertJsonFragment(['nome' => 'Dev']);
    }

    public function test_endpoint_lideres_exclui_proprio(): void
    {
        $a = Colaborador::create(['empresa_id' => $this->emp->id, 'nome' => 'Chefe', 'status' => 'ATIVO']);
        $b = Colaborador::create(['empresa_id' => $this->emp->id, 'nome' => 'Outro', 'status' => 'ATIVO']);

        $resp = $this->getJson(route('api.lideres', ['empresa_id' => $this->emp->id, 'excluir_id' => $a->id]))->assertOk()->json();
        $ids = collect($resp)->pluck('id');
        $this->assertFalse($ids->contains($a->id));
        $this->assertTrue($ids->contains($b->id));
    }

    public function test_endpoint_cnpj_invalido(): void
    {
        $this->getJson(route('api.cnpj', ['cnpj' => '123']))->assertStatus(422);
    }

    public function test_endpoints_exigem_autenticacao(): void
    {
        auth()->logout();
        $this->get(route('api.setores'))->assertRedirect();
    }
}
