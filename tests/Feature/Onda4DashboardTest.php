<?php

namespace Tests\Feature;

use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\Ocorrencia;
use App\Models\TipoOcorrencia;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Onda4DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renderiza_com_graficos_e_ranking(): void
    {
        $this->seed();
        $admin = Usuario::where('email', 'admin@tallents.com.br')->firstOrFail();
        $this->actingAs($admin);

        $emp = Empresa::firstOrFail();
        $colab = Colaborador::create(['empresa_id' => $emp->id, 'nome' => 'Ana', 'status' => 'ATIVO']);
        $tipo = TipoOcorrencia::create(['empresa_id' => $emp->id, 'nome' => 'Atraso', 'ativo' => true]);
        foreach (range(1, 3) as $d) {
            $colab->ocorrencias()->create([
                'empresa_id' => $emp->id, 'tipo_ocorrencia_id' => $tipo->id,
                'registrado_por' => $admin->id, 'data_ocorrencia' => now()->format('Y-m-d'),
            ]);
        }

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Ocorrências por mês')
            ->assertSee('Ranking de ocorrências')
            ->assertSee('Ana')
            ->assertSee('chartOcorrencias', false);
    }
}
