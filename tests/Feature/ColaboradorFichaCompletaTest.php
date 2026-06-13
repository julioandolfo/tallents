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

class ColaboradorFichaCompletaTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $emp;
    private Setor $setor;
    private Cargo $cargo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(Usuario::where('email', 'admin@tallents.com.br')->firstOrFail());
        $this->emp = Empresa::firstOrFail();
        $this->setor = Setor::create(['empresa_id' => $this->emp->id, 'nome' => 'TI']);
        $this->cargo = Cargo::create(['empresa_id' => $this->emp->id, 'nome' => 'Dev', 'salario_base' => 1]);
    }

    public function test_cria_colaborador_com_data_demissao_e_persiste(): void
    {
        $this->post(route('colaboradores.store'), [
            'nome' => 'Ana', 'empresa_id' => $this->emp->id, 'setor_id' => $this->setor->id, 'cargo_id' => $this->cargo->id,
            'status' => 'INATIVO', 'data_admissao' => '2023-01-10', 'data_demissao' => '2024-05-20',
            'regime_trabalho' => 'CLT',
        ])->assertRedirect();

        $colab = Colaborador::where('nome', 'Ana')->firstOrFail();
        $this->assertEquals('2024-05-20', $colab->data_demissao->format('Y-m-d'));
        $this->assertEquals('CLT', $colab->tipo_contrato);
        $this->assertEquals('CLT', $colab->regime_trabalho); // accessor
    }

    public function test_data_demissao_nao_pode_ser_antes_da_admissao(): void
    {
        $this->post(route('colaboradores.store'), [
            'nome' => 'Bia', 'empresa_id' => $this->emp->id, 'status' => 'INATIVO',
            'data_admissao' => '2024-05-20', 'data_demissao' => '2023-01-10',
        ])->assertSessionHasErrors('data_demissao');
    }

    public function test_show_exibe_secao_contratos_e_acesso(): void
    {
        $colab = Colaborador::create(['empresa_id' => $this->emp->id, 'setor_id' => $this->setor->id, 'cargo_id' => $this->cargo->id, 'nome' => 'Carlos', 'status' => 'ATIVO']);
        Contrato::create(['colaborador_id' => $colab->id, 'criado_por' => auth()->id(), 'titulo' => 'Contrato de Trabalho CLT', 'tipo' => 'CONTRATO', 'status' => 'PENDENTE']);

        $this->get(route('colaboradores.show', $colab))
            ->assertOk()
            ->assertSee('Contratos e Documentos')
            ->assertSee('Contrato de Trabalho CLT')
            ->assertSee('Acesso ao Sistema')
            ->assertSee('não possui acesso ao sistema');
    }

    public function test_show_exibe_acesso_quando_colaborador_tem_usuario(): void
    {
        $colab = Colaborador::create(['empresa_id' => $this->emp->id, 'nome' => 'Diana', 'status' => 'ATIVO', 'email_login' => 'diana@x.com']);
        Usuario::create(['name' => 'Diana', 'email' => 'diana@x.com', 'password' => bcrypt('x'), 'role' => 'COLABORADOR', 'empresa_id' => $this->emp->id, 'colaborador_id' => $colab->id, 'ativo' => true]);

        $this->get(route('colaboradores.show', $colab))
            ->assertOk()
            ->assertSee('diana@x.com')
            ->assertSee('Colaborador');
    }

    public function test_botao_novo_contrato_aponta_para_o_colaborador(): void
    {
        $colab = Colaborador::create(['empresa_id' => $this->emp->id, 'nome' => 'Eva', 'status' => 'ATIVO']);

        $this->get(route('colaboradores.show', $colab))
            ->assertOk()
            ->assertSee(route('contratos.create', ['colaborador_id' => $colab->id]), false);
    }

    public function test_edicao_preserva_data_demissao(): void
    {
        $colab = Colaborador::create(['empresa_id' => $this->emp->id, 'nome' => 'Fabio', 'status' => 'ATIVO', 'data_admissao' => '2020-01-01']);

        $this->put(route('colaboradores.update', $colab), [
            'nome' => 'Fabio', 'empresa_id' => $this->emp->id, 'status' => 'INATIVO',
            'data_admissao' => '2020-01-01', 'data_demissao' => '2024-06-01',
        ])->assertRedirect();

        $this->assertEquals('2024-06-01', $colab->fresh()->data_demissao->format('Y-m-d'));
    }
}
