<?php

namespace Tests\Feature;

use App\Models\Cargo;
use App\Models\Colaborador;
use App\Models\ColaboradorBonus;
use App\Models\Empresa;
use App\Models\FechamentoPagamento;
use App\Models\HoraExtra;
use App\Models\Setor;
use App\Models\TipoBonus;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class Onda2FechamentoBonusTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $emp;
    private Colaborador $colab;
    private Usuario $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Mail::fake();
        $this->admin = Usuario::where('email', 'admin@tallents.com.br')->firstOrFail();
        $this->actingAs($this->admin);

        $this->emp = Empresa::firstOrFail();
        $setor = Setor::create(['empresa_id' => $this->emp->id, 'nome' => 'TI']);
        $cargo = Cargo::create(['empresa_id' => $this->emp->id, 'nome' => 'Dev', 'salario_base' => 1]);
        $this->colab = Colaborador::create(['empresa_id' => $this->emp->id, 'setor_id' => $setor->id, 'cargo_id' => $cargo->id, 'nome' => 'Ana', 'status' => 'ATIVO', 'salario' => 3000]);
    }

    public function test_bonus_vigentes_em_por_janela(): void
    {
        $tipo = TipoBonus::create(['empresa_id' => $this->emp->id, 'nome' => 'Meta', 'ativo' => true]);
        // Vigente em março/2024
        $this->colab->bonus()->create(['tipo_bonus_id' => $tipo->id, 'valor' => 500, 'data_inicio' => '2024-01-01', 'data_fim' => '2024-12-31', 'ativo' => true]);
        // Fora da janela (só fevereiro)
        $this->colab->bonus()->create(['tipo_bonus_id' => $tipo->id, 'valor' => 999, 'data_inicio' => '2024-02-01', 'data_fim' => '2024-02-28', 'ativo' => true]);
        // Sem datas = sempre vigente
        $this->colab->bonus()->create(['tipo_bonus_id' => $tipo->id, 'valor' => 100, 'ativo' => true]);

        $total = ColaboradorBonus::where('colaborador_id', $this->colab->id)->vigentesEm(3, 2024)->sum('valor');
        $this->assertEquals(600.0, (float) $total); // 500 + 100, exclui o de fevereiro
    }

    public function test_fechar_calcula_itens_com_colunas_corretas(): void
    {
        HoraExtra::create(['empresa_id' => $this->emp->id, 'colaborador_id' => $this->colab->id, 'registrado_por' => $this->admin->id, 'data' => '2024-03-05', 'horas' => 2, 'valor' => 200, 'status' => 'APROVADO']);
        $tipo = TipoBonus::create(['empresa_id' => $this->emp->id, 'nome' => 'Meta', 'ativo' => true]);
        $this->colab->bonus()->create(['tipo_bonus_id' => $tipo->id, 'valor' => 500, 'ativo' => true]);

        $fech = FechamentoPagamento::create(['empresa_id' => $this->emp->id, 'mes' => 3, 'ano' => 2024, 'status' => 'ABERTO', 'criado_por' => $this->admin->id]);

        $this->post(route('fechamentos.fechar', $fech), ['colaborador_ids' => [$this->colab->id]])->assertRedirect();

        $item = $fech->itens()->firstOrFail();
        $this->assertEquals(3000.0, (float) $item->salario_base);
        $this->assertEquals(200.0, (float) $item->total_horas_extras);
        $this->assertEquals(500.0, (float) $item->total_bonus);
        $this->assertEquals(3700.0, (float) $item->total);
        $this->assertEquals('FECHADO', $fech->fresh()->status);
        $this->assertEquals(3700.0, (float) $fech->fresh()->total_geral);
    }

    public function test_editar_item_recalcula_total(): void
    {
        $fech = FechamentoPagamento::create(['empresa_id' => $this->emp->id, 'mes' => 3, 'ano' => 2024, 'status' => 'ABERTO', 'criado_por' => $this->admin->id]);
        $item = $fech->itens()->create(['colaborador_id' => $this->colab->id, 'salario_base' => 3000, 'total_horas_extras' => 0, 'total_bonus' => 0, 'descontos' => 0, 'adicionais' => 0, 'total' => 3000]);

        $this->put(route('fechamentos.itens.update', $item), ['descontos' => 200, 'adicionais' => 50])->assertRedirect();

        $item->refresh();
        $this->assertEquals(200.0, (float) $item->descontos);
        $this->assertEquals(2850.0, (float) $item->total); // 3000 - 200 + 50
        $this->assertEquals(2850.0, (float) $fech->fresh()->total_geral);
    }

    public function test_reabrir_volta_para_aberto(): void
    {
        $fech = FechamentoPagamento::create(['empresa_id' => $this->emp->id, 'mes' => 3, 'ano' => 2024, 'status' => 'FECHADO', 'criado_por' => $this->admin->id, 'fechado_por' => $this->admin->id, 'fechado_em' => now()]);
        $this->post(route('fechamentos.reabrir', $fech))->assertRedirect();
        $this->assertEquals('ABERTO', $fech->fresh()->status);
    }

    public function test_bonus_por_colaborador_na_ficha(): void
    {
        $tipo = TipoBonus::create(['empresa_id' => $this->emp->id, 'nome' => 'Meta', 'ativo' => true]);

        $this->post(route('colaboradores.bonus.store', $this->colab), [
            'tipo_bonus_id' => $tipo->id, 'valor' => 250, 'data_inicio' => '2024-01-01', 'ativo' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('colaboradores_bonus', ['colaborador_id' => $this->colab->id, 'valor' => 250]);

        $bonus = $this->colab->bonus()->firstOrFail();
        $this->delete(route('colaboradores.bonus.destroy', $bonus))->assertRedirect();
        $this->assertDatabaseMissing('colaboradores_bonus', ['id' => $bonus->id]);
    }
}
