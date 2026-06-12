<?php

namespace Tests\Feature;

use App\Models\Cargo;
use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\Setor;
use App\Models\TipoOcorrencia;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OcorrenciaPontoTest extends TestCase
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

    public function test_tipo_ocorrencia_salva_categoria_e_flags(): void
    {
        $this->post(route('tipos-ocorrencias.store'), [
            'nome' => 'Atraso', 'categoria' => 'ATRASO',
            'permite_tempo_atraso' => '1', 'permite_tipo_ponto' => '1', 'ativo' => '1',
        ])->assertRedirect();

        $tipo = TipoOcorrencia::where('nome', 'Atraso')->firstOrFail();
        $this->assertEquals('ATRASO', $tipo->categoria);
        $this->assertTrue($tipo->permite_tempo_atraso);
        $this->assertTrue($tipo->permite_tipo_ponto);
    }

    public function test_ocorrencia_salva_hora_atraso_e_ponto(): void
    {
        $tipo = TipoOcorrencia::create([
            'empresa_id' => $this->colab->empresa_id, 'nome' => 'Atraso', 'categoria' => 'ATRASO',
            'permite_tempo_atraso' => true, 'permite_tipo_ponto' => true, 'ativo' => true,
        ]);

        $this->post(route('ocorrencias.store'), [
            'colaborador_id' => $this->colab->id, 'tipo_ocorrencia_id' => $tipo->id,
            'data' => '2024-03-10', 'hora_ocorrencia' => '08:45',
            'tempo_atraso_minutos' => 15, 'tipo_ponto' => 'ENTRADA',
        ])->assertRedirect();

        $this->assertDatabaseHas('ocorrencias', [
            'colaborador_id' => $this->colab->id,
            'tempo_atraso_minutos' => 15,
            'tipo_ponto' => 'ENTRADA',
        ]);
    }

    public function test_forms_renderizam_campos_ponto(): void
    {
        $this->get(route('tipos-ocorrencias.create'))->assertOk()->assertSee('tempo de atraso');
        $this->get(route('ocorrencias.create'))->assertOk()
            ->assertSee('name="tempo_atraso_minutos"', false)
            ->assertSee('name="tipo_ponto"', false)
            ->assertSee('name="hora_ocorrencia"', false);
    }
}
