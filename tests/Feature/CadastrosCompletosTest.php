<?php

namespace Tests\Feature;

use App\Models\Cargo;
use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CadastrosCompletosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(Usuario::where('email', 'admin@tallents.com.br')->firstOrFail());
    }

    public function test_empresa_salva_percentual_hora_extra_e_ativa(): void
    {
        $this->post(route('empresas.store'), [
            'nome' => 'ACME', 'percentual_hora_extra' => 75, 'ativa' => '1', 'bairro' => 'Centro',
        ])->assertRedirect();

        $emp = Empresa::where('nome', 'ACME')->firstOrFail();
        $this->assertEquals('75.00', (string) $emp->percentual_hora_extra);
        $this->assertTrue((bool) $emp->ativa);
        $this->assertEquals('Centro', $emp->bairro);
    }

    public function test_cargo_salva_empresa_e_ativo(): void
    {
        $emp = Empresa::firstOrFail();
        $this->post(route('cargos.store'), [
            'nome' => 'Analista', 'empresa_id' => $emp->id, 'salario_base' => 3000, 'salario_maximo' => 5000, 'ativo' => '1',
        ])->assertRedirect();

        $cargo = Cargo::where('nome', 'Analista')->firstOrFail();
        $this->assertEquals($emp->id, $cargo->empresa_id);
        $this->assertTrue((bool) $cargo->ativo);
    }

    public function test_usuario_salva_cpf_empresas_colaborador_e_status(): void
    {
        $emp = Empresa::firstOrFail();
        $colab = Colaborador::create(['empresa_id' => $emp->id, 'nome' => 'Vinculado', 'status' => 'ATIVO']);

        $this->post(route('usuarios.store'), [
            'name' => 'Gestor X', 'email' => 'gestorx@tallents.com.br', 'cpf' => '123.456.789-00',
            'perfil' => 'gestor', 'colaborador_id' => $colab->id, 'empresa_ids' => [$emp->id],
            'password' => 'segredo123', 'password_confirmation' => 'segredo123', 'ativo' => '1',
        ])->assertRedirect();

        $user = Usuario::where('email', 'gestorx@tallents.com.br')->firstOrFail();
        $this->assertEquals('GESTOR', $user->role);
        $this->assertEquals('123.456.789-00', $user->cpf);
        $this->assertEquals($colab->id, $user->colaborador_id);
        $this->assertTrue($user->empresas->contains($emp->id));
    }

    public function test_forms_renderizam_campos_novos(): void
    {
        $emp = Empresa::firstOrFail();
        $cargo = Cargo::create(['empresa_id' => $emp->id, 'nome' => 'C', 'salario_base' => 1]);
        $user = Usuario::where('email', 'admin@tallents.com.br')->firstOrFail();

        $this->get(route('empresas.create'))->assertOk()->assertSee('Percentual de Hora Extra');
        $this->get(route('empresas.edit', $emp))->assertOk()->assertSee('Percentual de Hora Extra');
        $this->get(route('cargos.create'))->assertOk()->assertSee('name="empresa_id"', false);
        $this->get(route('cargos.edit', $cargo))->assertOk()->assertSee('Salário Máximo');
        $this->get(route('usuarios.create'))->assertOk()->assertSee('Colaborador vinculado')->assertSee('name="cpf"', false);
        $this->get(route('usuarios.edit', $user))->assertOk()->assertSee('Empresas com acesso');
    }
}
