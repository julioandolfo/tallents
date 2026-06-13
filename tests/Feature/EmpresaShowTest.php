<?php

namespace Tests\Feature;

use App\Models\Cargo;
use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\Setor;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmpresaShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_da_empresa_lista_em_abas(): void
    {
        $this->seed();
        $this->actingAs(Usuario::where('email', 'admin@tallents.com.br')->firstOrFail());

        $emp = Empresa::create(['nome' => 'Azzo Distribuidora', 'ativa' => true, 'percentual_hora_extra' => 50]);
        $setor = Setor::create(['empresa_id' => $emp->id, 'nome' => 'Representantes']);
        $cargo = Cargo::create(['empresa_id' => $emp->id, 'nome' => 'Representante Comercial', 'salario_base' => 3000]);
        Colaborador::create(['empresa_id' => $emp->id, 'setor_id' => $setor->id, 'cargo_id' => $cargo->id, 'nome' => 'Alexandre Barduk', 'status' => 'INATIVO']);

        $resp = $this->get(route('empresas.show', $emp))->assertOk();

        // Abas
        foreach (['Visão Geral', 'Colaboradores', 'Setores', 'Cargos'] as $aba) {
            $resp->assertSee($aba, false);
        }
        $resp->assertSee("tab = 'geral'", false);

        // Conteúdo das abas (antes setores/cargos eram carregados mas nunca exibidos)
        $resp->assertSee('Alexandre Barduk');           // aba colaboradores
        $resp->assertSee('Representantes');               // aba setores
        $resp->assertSee('Representante Comercial');      // aba cargos
        $resp->assertSee('R$ 3.000,00');                  // salário base do cargo
        $resp->assertSee(route('colaboradores.show', Colaborador::where('nome', 'Alexandre Barduk')->first()), false);
    }
}
