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

    public function test_show_exibe_modulos_orfaos_e_links(): void
    {
        $colab = Colaborador::create(['empresa_id' => $this->emp->id, 'setor_id' => $this->setor->id, 'cargo_id' => $this->cargo->id, 'nome' => 'Gustavo', 'status' => 'ATIVO']);

        \App\Models\Advertencia::create(['empresa_id' => $this->emp->id, 'colaborador_id' => $colab->id, 'aplicada_por' => auth()->id(), 'tipo' => 'VERBAL', 'motivo' => 'Atraso reiterado', 'data' => '2024-03-01']);
        \App\Models\Avaliacao::create(['colaborador_id' => $colab->id, 'avaliador_id' => auth()->id(), 'ciclo' => '2024.1', 'tipo' => 'GESTOR', 'nota_geral' => 8.5, 'status' => 'CONCLUIDA', 'data' => '2024-03-01']);
        \App\Models\Pdi::create(['colaborador_id' => $colab->id, 'responsavel_id' => auth()->id(), 'titulo' => 'Liderança técnica', 'status' => 'EM_ANDAMENTO', 'prazo' => '2024-12-31']);
        \App\Models\Onboarding::create(['colaborador_id' => $colab->id, 'empresa_id' => $this->emp->id, 'responsavel_id' => auth()->id(), 'data_inicio' => '2024-01-05', 'status' => 'EM_ANDAMENTO']);
        \App\Models\Feedback::create(['colaborador_id' => $colab->id, 'autor_id' => auth()->id(), 'tipo' => 'POSITIVO', 'mensagem' => 'Excelente trabalho', 'data' => '2024-03-10']);
        \App\Models\BancoHorasMovimentacao::create(['colaborador_id' => $colab->id, 'empresa_id' => $this->emp->id, 'registrado_por' => auth()->id(), 'tipo' => 'CREDITO', 'horas' => 4, 'motivo' => 'Hora extra', 'data' => '2024-03-12']);

        $resp = $this->get(route('colaboradores.show', $colab))->assertOk();

        $resp->assertSee('Advertências')->assertSee('Atraso reiterado');
        $resp->assertSee('Avaliações de Desempenho')->assertSee('2024.1');
        $resp->assertSee('Plano de Desenvolvimento (PDI)')->assertSee('Liderança técnica');
        $resp->assertSee('Onboarding');
        $resp->assertSee('Feedbacks')->assertSee('Excelente trabalho');
        $resp->assertSee('Banco de Horas')->assertSee('4,00h');

        // Links de criação pré-vinculados ao colaborador.
        $resp->assertSee(route('advertencias.create', ['colaborador_id' => $colab->id]), false);
        $resp->assertSee(route('pdis.create', ['colaborador_id' => $colab->id]), false);
        $resp->assertSee(route('banco-horas.extrato', $colab), false);
    }

    public function test_show_tem_abas_e_renderiza_campos_corrigidos(): void
    {
        $colab = Colaborador::create(['empresa_id' => $this->emp->id, 'setor_id' => $this->setor->id, 'cargo_id' => $this->cargo->id, 'nome' => 'Igor', 'status' => 'ATIVO', 'salario' => 2000]);
        $tipo = \App\Models\TipoOcorrencia::create(['empresa_id' => $this->emp->id, 'nome' => 'Atraso', 'ativo' => true]);
        $colab->ocorrencias()->create(['empresa_id' => $this->emp->id, 'tipo_ocorrencia_id' => $tipo->id, 'registrado_por' => auth()->id(), 'data_ocorrencia' => '2024-04-10', 'descricao' => 'Chegou 30min atrasado']);
        \App\Models\HoraExtra::create(['empresa_id' => $this->emp->id, 'colaborador_id' => $colab->id, 'registrado_por' => auth()->id(), 'data' => '2024-04-12', 'horas' => 3, 'valor' => 90, 'status' => 'APROVADO']);
        $novo = Cargo::create(['empresa_id' => $this->emp->id, 'nome' => 'Senior', 'salario_base' => 4000]);
        \App\Models\Promocao::create(['empresa_id' => $this->emp->id, 'colaborador_id' => $colab->id, 'registrado_por' => auth()->id(), 'tipo' => 'PROMOCAO', 'cargo_anterior_id' => $this->cargo->id, 'cargo_novo_id' => $novo->id, 'salario_anterior' => 2000, 'salario_novo' => 4000, 'data_promocao' => '2024-04-15']);

        $resp = $this->get(route('colaboradores.show', $colab))->assertOk();

        // Navegação por abas presente.
        foreach (['Visão Geral', 'Ocorrências', 'Horas &amp; Bônus', 'Carreira', 'Pessoal', 'Contratos'] as $aba) {
            $resp->assertSee($aba, false);
        }
        $resp->assertSee("tab = 'geral'", false); // Alpine x-data

        // Campos que antes liam atributos inexistentes (data/observacao/quantidade/novoCargoObj/novo_salario).
        $resp->assertSee('Chegou 30min atrasado');   // ocorrencia->descricao
        $resp->assertSee('10/04/2024');               // ocorrencia->data_ocorrencia
        $resp->assertSee('3h');                        // hora_extra->horas
        $resp->assertSee('Senior');                    // promocao->cargoNovo
        $resp->assertSee('R$ 4.000,00');               // promocao->salario_novo
    }

    public function test_avaliacao_create_preseleciona_colaborador(): void
    {
        $colab = Colaborador::create(['empresa_id' => $this->emp->id, 'nome' => 'Helena', 'status' => 'ATIVO']);

        $this->get(route('avaliacoes.create', ['colaborador_id' => $colab->id]))
            ->assertOk()
            ->assertSee('value="' . $colab->id . '" selected', false);
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
