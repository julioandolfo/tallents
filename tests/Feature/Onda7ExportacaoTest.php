<?php

namespace Tests\Feature;

use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\FechamentoPagamento;
use App\Models\Setor;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Onda7ExportacaoTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $empA;
    private Empresa $empB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->empA = Empresa::create(['nome' => 'Empresa A', 'ativa' => true]);
        $this->empB = Empresa::create(['nome' => 'Empresa B', 'ativa' => true]);
        Colaborador::create(['empresa_id' => $this->empA->id, 'nome' => 'Ana da A', 'status' => 'ATIVO', 'cpf' => '111']);
        Colaborador::create(['empresa_id' => $this->empB->id, 'nome' => 'Bruno da B', 'status' => 'ATIVO', 'cpf' => '222']);
    }

    public function test_csv_colaboradores_admin(): void
    {
        $this->actingAs(Usuario::where('email', 'admin@tallents.com.br')->firstOrFail());

        $resp = $this->get(route('exportar.colaboradores.csv'));
        $resp->assertOk();
        $resp->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $conteudo = $resp->streamedContent();
        $this->assertStringContainsString('Ana da A', $conteudo);
        $this->assertStringContainsString('Bruno da B', $conteudo);
        $this->assertStringContainsString('Nome;CPF;Empresa', $conteudo);
    }

    public function test_csv_respeita_escopo_do_rh(): void
    {
        $rh = Usuario::create(['name' => 'RH A', 'email' => 'rh@x.com', 'password' => bcrypt('x'), 'role' => 'RH', 'empresa_id' => $this->empA->id, 'ativo' => true]);
        $this->actingAs($rh);

        $conteudo = $this->get(route('exportar.colaboradores.csv'))->assertOk()->streamedContent();
        $this->assertStringContainsString('Ana da A', $conteudo);
        $this->assertStringNotContainsString('Bruno da B', $conteudo);
    }

    public function test_pdf_colaboradores(): void
    {
        $this->actingAs(Usuario::where('email', 'admin@tallents.com.br')->firstOrFail());

        $resp = $this->get(route('exportar.colaboradores.pdf'));
        $resp->assertOk();
        $resp->assertHeader('content-type', 'application/pdf');
    }

    public function test_pdf_fechamento(): void
    {
        $admin = Usuario::where('email', 'admin@tallents.com.br')->firstOrFail();
        $this->actingAs($admin);

        $fech = FechamentoPagamento::create(['empresa_id' => $this->empA->id, 'mes' => 3, 'ano' => 2024, 'status' => 'FECHADO', 'criado_por' => $admin->id]);
        $colab = Colaborador::where('empresa_id', $this->empA->id)->first();
        $fech->itens()->create(['colaborador_id' => $colab->id, 'salario_base' => 1000, 'total_horas_extras' => 0, 'total_bonus' => 0, 'descontos' => 0, 'adicionais' => 0, 'total' => 1000]);

        $this->get(route('exportar.fechamentos.pdf', $fech))->assertOk()->assertHeader('content-type', 'application/pdf');
    }

    public function test_colaborador_nao_acessa_exportacao(): void
    {
        $colab = Colaborador::where('empresa_id', $this->empA->id)->first();
        $user = Usuario::create(['name' => 'Colab', 'email' => 'c@x.com', 'password' => bcrypt('x'), 'role' => 'COLABORADOR', 'empresa_id' => $this->empA->id, 'colaborador_id' => $colab->id, 'ativo' => true]);
        $this->actingAs($user);

        // papel:ADMIN,RH,GESTOR redireciona COLABORADOR para o portal.
        $this->get(route('exportar.colaboradores.csv'))->assertRedirect(route('portal.index'));
    }
}
