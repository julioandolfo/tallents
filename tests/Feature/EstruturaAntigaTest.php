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

class EstruturaAntigaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(Usuario::where('email', 'admin@tallents.com.br')->firstOrFail());
    }

    public function test_organograma_monta_hierarquia_por_lider(): void
    {
        $emp = Empresa::firstOrFail();
        $chefe = Colaborador::create(['empresa_id' => $emp->id, 'nome' => 'Chefe', 'status' => 'ATIVO']);
        $sub = Colaborador::create(['empresa_id' => $emp->id, 'nome' => 'Subordinado', 'status' => 'ATIVO', 'lider_id' => $chefe->id]);

        $this->get(route('organograma.index', ['empresa_id' => $emp->id]))
            ->assertOk()
            ->assertSee('Chefe')
            ->assertSee('Subordinado');
    }

    public function test_relatorio_ocorrencias_agrega(): void
    {
        $emp = Empresa::firstOrFail();
        $setor = Setor::create(['empresa_id' => $emp->id, 'nome' => 'TI']);
        $cargo = Cargo::create(['empresa_id' => $emp->id, 'nome' => 'Dev', 'salario_base' => 1]);
        $colab = Colaborador::create(['empresa_id' => $emp->id, 'setor_id' => $setor->id, 'cargo_id' => $cargo->id, 'nome' => 'Ana', 'status' => 'ATIVO']);
        $tipo = TipoOcorrencia::create(['empresa_id' => $emp->id, 'nome' => 'Atraso', 'ativo' => true]);

        $colab->ocorrencias()->create([
            'empresa_id' => $emp->id, 'tipo_ocorrencia_id' => $tipo->id, 'registrado_por' => auth()->id(),
            'data_ocorrencia' => '2024-03-10', 'gravidade' => 'leve', 'tempo_atraso_minutos' => 20,
        ]);

        $this->get(route('ocorrencias.relatorio'))
            ->assertOk()
            ->assertSee('Relatório de Ocorrências')
            ->assertSee('Atraso')
            ->assertSee('Ana');
    }

    public function test_menu_segue_estrutura_antiga(): void
    {
        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Estrutura')
            ->assertSee('Hierarquia')
            ->assertSee('Organograma')
            ->assertSee('Listar Colaboradores')
            ->assertSee('Fechamento de Pagamentos')
            ->assertSee('Listar Ocorrências')
            ->assertSee('Sistema');
    }
}
