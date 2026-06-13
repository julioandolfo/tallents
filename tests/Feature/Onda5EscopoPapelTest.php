<?php

namespace Tests\Feature;

use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\Setor;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Onda5EscopoPapelTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $empA;
    private Empresa $empB;
    private Colaborador $colabA;
    private Colaborador $colabB;
    private Setor $setorA1;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->empA = Empresa::create(['nome' => 'Empresa A', 'ativa' => true]);
        $this->empB = Empresa::create(['nome' => 'Empresa B', 'ativa' => true]);
        $this->setorA1 = Setor::create(['empresa_id' => $this->empA->id, 'nome' => 'Vendas']);
        $setorA2 = Setor::create(['empresa_id' => $this->empA->id, 'nome' => 'TI']);

        $this->colabA = Colaborador::create(['empresa_id' => $this->empA->id, 'setor_id' => $this->setorA1->id, 'nome' => 'Ana da A', 'status' => 'ATIVO']);
        $this->colabB = Colaborador::create(['empresa_id' => $this->empB->id, 'nome' => 'Bruno da B', 'status' => 'ATIVO']);
        // Colaborador da empresa A em outro setor (não deve aparecer p/ gestor de Vendas).
        Colaborador::create(['empresa_id' => $this->empA->id, 'setor_id' => $setorA2->id, 'nome' => 'Carlos TI', 'status' => 'ATIVO']);
    }

    private function rhDaEmpresaA(): Usuario
    {
        return Usuario::create(['name' => 'RH A', 'email' => 'rh.a@x.com', 'password' => bcrypt('x'), 'role' => 'RH', 'empresa_id' => $this->empA->id, 'ativo' => true]);
    }

    public function test_rh_so_ve_colaboradores_da_sua_empresa(): void
    {
        $this->actingAs($this->rhDaEmpresaA());

        $this->get(route('colaboradores.index'))
            ->assertOk()
            ->assertSee('Ana da A')
            ->assertDontSee('Bruno da B');
    }

    public function test_rh_recebe_403_em_colaborador_de_outra_empresa(): void
    {
        $this->actingAs($this->rhDaEmpresaA());

        $this->get(route('colaboradores.show', $this->colabA))->assertOk();
        $this->get(route('colaboradores.show', $this->colabB))->assertForbidden();
    }

    public function test_gestor_so_ve_colaboradores_do_seu_setor(): void
    {
        $gestor = Usuario::create(['name' => 'Gestor Vendas', 'email' => 'g@x.com', 'password' => bcrypt('x'), 'role' => 'GESTOR', 'empresa_id' => $this->empA->id, 'setor_id' => $this->setorA1->id, 'ativo' => true]);
        $this->actingAs($gestor);

        $this->get(route('colaboradores.index'))
            ->assertOk()
            ->assertSee('Ana da A')
            ->assertDontSee('Carlos TI')
            ->assertDontSee('Bruno da B');
    }

    public function test_admin_ve_todos(): void
    {
        $this->actingAs(Usuario::where('email', 'admin@tallents.com.br')->firstOrFail());

        $this->get(route('colaboradores.index'))
            ->assertOk()
            ->assertSee('Ana da A')
            ->assertSee('Bruno da B');
    }

    public function test_rh_so_ve_a_propria_empresa_na_listagem(): void
    {
        $this->actingAs($this->rhDaEmpresaA());

        $this->get(route('empresas.index'))
            ->assertOk()
            ->assertSee('Empresa A')
            ->assertDontSee('Empresa B');
    }
}
