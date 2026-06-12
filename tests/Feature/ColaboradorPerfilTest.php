<?php

namespace Tests\Feature;

use App\Models\Cargo;
use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\Setor;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ColaboradorPerfilTest extends TestCase
{
    use RefreshDatabase;

    private Colaborador $colab;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(Usuario::where('email', 'admin@tallents.com.br')->firstOrFail());

        $emp = Empresa::firstOrFail();
        $setor = Setor::create(['empresa_id' => $emp->id, 'nome' => 'TI']);
        $cargo = Cargo::create(['empresa_id' => $emp->id, 'nome' => 'Dev', 'salario_base' => 1]);
        $this->colab = Colaborador::create(['empresa_id' => $emp->id, 'setor_id' => $setor->id, 'cargo_id' => $cargo->id, 'nome' => 'Ana', 'status' => 'ATIVO']);
    }

    public function test_adicionar_e_remover_dependente(): void
    {
        $this->post(route('dependentes.store', $this->colab), [
            'nome' => 'João', 'parentesco' => 'FILHO', 'dependente_ir' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('dependentes', ['colaborador_id' => $this->colab->id, 'nome' => 'João', 'dependente_ir' => true]);

        $dep = $this->colab->dependentes()->first();
        $this->delete(route('dependentes.destroy', $dep))->assertRedirect();
        $this->assertDatabaseMissing('dependentes', ['id' => $dep->id]);
    }

    public function test_adicionar_e_remover_formacao(): void
    {
        $this->post(route('formacoes.store', $this->colab), [
            'nivel' => 'GRADUACAO', 'curso' => 'Sistemas', 'situacao' => 'CONCLUIDO', 'ano_conclusao' => 2020,
        ])->assertRedirect();

        $this->assertDatabaseHas('formacoes', ['colaborador_id' => $this->colab->id, 'curso' => 'Sistemas']);

        $form = $this->colab->formacoes()->first();
        $this->delete(route('formacoes.destroy', $form))->assertRedirect();
        $this->assertDatabaseMissing('formacoes', ['id' => $form->id]);
    }

    public function test_ficha_renderiza_com_secoes(): void
    {
        $this->colab->dependentes()->create(['nome' => 'Maria', 'parentesco' => 'CONJUGE']);
        $this->colab->formacoes()->create(['nivel' => 'MEDIO', 'curso' => 'Ensino Médio', 'situacao' => 'CONCLUIDO']);

        $this->get(route('colaboradores.show', $this->colab))
            ->assertOk()
            ->assertSee('Dependentes')
            ->assertSee('Formação acadêmica');
    }
}
