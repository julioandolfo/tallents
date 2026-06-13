<?php

namespace Tests\Feature;

use App\Models\Cargo;
use App\Models\Colaborador;
use App\Models\ColaboradorBonus;
use App\Models\Empresa;
use App\Models\NivelHierarquico;
use App\Models\Setor;
use App\Models\TipoBonus;
use App\Models\TipoOcorrencia;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Onda0CorrecoesTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = Usuario::where('email', 'admin@tallents.com.br')->firstOrFail();
        $this->actingAs($this->admin);
    }

    public function test_busca_de_empresas_funciona(): void
    {
        Empresa::create(['nome' => 'Alpha Tech', 'ativa' => true]);
        Empresa::create(['nome' => 'Beta Corp', 'ativa' => true]);

        $this->get(route('empresas.index', ['search' => 'Alpha']))
            ->assertOk()->assertSee('Alpha Tech')->assertDontSee('Beta Corp');
    }

    public function test_filtros_de_ocorrencias_funcionam(): void
    {
        $emp = Empresa::firstOrFail();
        $setor = Setor::create(['empresa_id' => $emp->id, 'nome' => 'TI']);
        $cargo = Cargo::create(['empresa_id' => $emp->id, 'nome' => 'Dev', 'salario_base' => 1]);
        $ana = Colaborador::create(['empresa_id' => $emp->id, 'setor_id' => $setor->id, 'cargo_id' => $cargo->id, 'nome' => 'Ana Lima', 'status' => 'ATIVO']);
        $bia = Colaborador::create(['empresa_id' => $emp->id, 'setor_id' => $setor->id, 'cargo_id' => $cargo->id, 'nome' => 'Bia Souza', 'status' => 'ATIVO']);
        $tipo = TipoOcorrencia::create(['empresa_id' => $emp->id, 'nome' => 'Atraso', 'ativo' => true]);
        $ana->ocorrencias()->create(['empresa_id' => $emp->id, 'tipo_ocorrencia_id' => $tipo->id, 'registrado_por' => $this->admin->id, 'data_ocorrencia' => '2024-01-10']);
        $bia->ocorrencias()->create(['empresa_id' => $emp->id, 'tipo_ocorrencia_id' => $tipo->id, 'registrado_por' => $this->admin->id, 'data_ocorrencia' => '2024-01-11']);

        $this->get(route('ocorrencias.index', ['search' => 'Ana']))
            ->assertOk()->assertSee('Ana Lima')->assertDontSee('Bia Souza');

        $this->get(route('ocorrencias.index', ['tipo_ocorrencia_id' => $tipo->id]))->assertOk()->assertSee('Ana Lima');
    }

    public function test_troca_de_senha_no_perfil(): void
    {
        $this->put(route('perfil.password'), [
            'senha_atual' => 'Tallents@2024',
            'password' => 'novaSenha123',
            'password_confirmation' => 'novaSenha123',
        ])->assertRedirect(route('perfil.index'))->assertSessionHasNoErrors();

        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('novaSenha123', $this->admin->fresh()->password));
    }

    public function test_nivel_em_uso_nao_e_removido(): void
    {
        $emp = Empresa::firstOrFail();
        $nivel = NivelHierarquico::create(['empresa_id' => $emp->id, 'nome' => 'Pleno', 'nivel' => 2]);
        Colaborador::create(['empresa_id' => $emp->id, 'nome' => 'X', 'status' => 'ATIVO', 'nivel_hierarquico_id' => $nivel->id]);

        $this->delete(route('niveis-hierarquicos.destroy', $nivel))->assertSessionHas('error');
        $this->assertDatabaseHas('niveis_hierarquicos', ['id' => $nivel->id]);
    }

    public function test_tipo_ocorrencia_em_uso_nao_e_removido(): void
    {
        $emp = Empresa::firstOrFail();
        $colab = Colaborador::create(['empresa_id' => $emp->id, 'nome' => 'X', 'status' => 'ATIVO']);
        $tipo = TipoOcorrencia::create(['empresa_id' => $emp->id, 'nome' => 'Falta', 'ativo' => true]);
        $colab->ocorrencias()->create(['empresa_id' => $emp->id, 'tipo_ocorrencia_id' => $tipo->id, 'registrado_por' => $this->admin->id, 'data_ocorrencia' => '2024-01-10']);

        $this->delete(route('tipos-ocorrencias.destroy', $tipo))->assertSessionHas('error');
        $this->assertDatabaseHas('tipos_ocorrencias', ['id' => $tipo->id]);
    }

    public function test_tipo_bonus_em_uso_nao_e_removido(): void
    {
        $emp = Empresa::firstOrFail();
        $colab = Colaborador::create(['empresa_id' => $emp->id, 'nome' => 'X', 'status' => 'ATIVO']);
        $tipo = TipoBonus::create(['empresa_id' => $emp->id, 'nome' => 'Meta', 'ativo' => true]);
        ColaboradorBonus::create(['colaborador_id' => $colab->id, 'tipo_bonus_id' => $tipo->id, 'valor' => 100, 'ativo' => true]);

        $this->delete(route('tipos-bonus.destroy', $tipo))->assertSessionHas('error');
        $this->assertDatabaseHas('tipos_bonus', ['id' => $tipo->id]);
    }
}
