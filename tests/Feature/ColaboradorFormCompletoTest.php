<?php

namespace Tests\Feature;

use App\Models\Cargo;
use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\Setor;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ColaboradorFormCompletoTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $empresa;
    private Cargo $cargo;
    private Setor $setor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(Usuario::where('email', 'admin@tallents.com.br')->firstOrFail());

        $this->empresa = Empresa::firstOrFail();
        $this->setor = Setor::create(['empresa_id' => $this->empresa->id, 'nome' => 'TI']);
        $this->cargo = Cargo::create(['empresa_id' => $this->empresa->id, 'nome' => 'Dev', 'salario_base' => 1]);
    }

    public function test_cria_colaborador_com_dados_bancarios_e_lider(): void
    {
        $lider = Colaborador::create(['empresa_id' => $this->empresa->id, 'nome' => 'Chefe', 'status' => 'ATIVO']);

        $this->post(route('colaboradores.store'), [
            'nome' => 'Ana Silva', 'cpf' => '111', 'empresa_id' => $this->empresa->id,
            'cargo_id' => $this->cargo->id, 'setor_id' => $this->setor->id, 'lider_id' => $lider->id,
            'status' => 'ATIVO', 'celular' => '(11) 99999-9999', 'observacoes' => 'Boa contratação',
            'banco' => '341 - Itaú', 'agencia' => '1234', 'conta' => '56789-0',
            'tipo_conta' => 'CORRENTE', 'pix' => 'ana@pix.com', 'cnpj' => '00.000.000/0001-00',
        ])->assertRedirect();

        $c = Colaborador::where('nome', 'Ana Silva')->firstOrFail();
        $this->assertEquals('341 - Itaú', $c->banco);
        $this->assertEquals('CORRENTE', $c->tipo_conta);
        $this->assertEquals('ana@pix.com', $c->pix);
        $this->assertEquals($lider->id, $c->lider_id);
        $this->assertEquals('(11) 99999-9999', $c->celular);
        $this->assertEquals('Boa contratação', $c->observacoes);
    }

    public function test_provisiona_acesso_ao_sistema(): void
    {
        $this->post(route('colaboradores.store'), [
            'nome' => 'Bia', 'empresa_id' => $this->empresa->id, 'cargo_id' => $this->cargo->id, 'status' => 'ATIVO',
            'email_login' => 'bia@tallents.com.br', 'password' => 'segredo123', 'password_confirmation' => 'segredo123', 'perfil' => 'rh',
        ])->assertRedirect();

        $c = Colaborador::where('nome', 'Bia')->firstOrFail();
        $user = Usuario::where('email', 'bia@tallents.com.br')->firstOrFail();
        $this->assertEquals('RH', $user->role);
        $this->assertEquals($c->id, $user->colaborador_id);
    }

    public function test_edicao_preserva_e_atualiza_dados_bancarios(): void
    {
        $c = Colaborador::create(['empresa_id' => $this->empresa->id, 'cargo_id' => $this->cargo->id, 'nome' => 'Carlos', 'status' => 'ATIVO', 'banco' => 'Antigo']);

        $this->put(route('colaboradores.update', $c), [
            'nome' => 'Carlos', 'empresa_id' => $this->empresa->id, 'cargo_id' => $this->cargo->id, 'status' => 'ATIVO',
            'banco' => 'Novo Banco', 'pix' => 'carlos@pix',
        ])->assertRedirect();

        $c->refresh();
        $this->assertEquals('Novo Banco', $c->banco);
        $this->assertEquals('carlos@pix', $c->pix);
    }

    public function test_create_e_edit_renderizam_todas_abas(): void
    {
        $c = Colaborador::create(['empresa_id' => $this->empresa->id, 'cargo_id' => $this->cargo->id, 'nome' => 'Dora', 'status' => 'ATIVO']);

        foreach ([route('colaboradores.create'), route('colaboradores.edit', $c)] as $url) {
            $this->get($url)->assertOk()
                ->assertSee('Dados Bancários')
                ->assertSee('Líder / Gestor direto')
                ->assertSee('Observações')
                ->assertSee('name="celular"', false)
                ->assertSee('name="pix"', false)
                ->assertSee('name="lider_id"', false);
        }
    }
}
