<?php

namespace Tests\Feature;

use App\Models\Cargo;
use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\Ocorrencia;
use App\Models\Setor;
use App\Models\TipoOcorrencia;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OcorrenciaDetalhesTest extends TestCase
{
    use RefreshDatabase;

    private function ocorrencia(): Ocorrencia
    {
        $emp = Empresa::firstOrFail();
        $setor = Setor::create(['empresa_id' => $emp->id, 'nome' => 'TI']);
        $cargo = Cargo::create(['empresa_id' => $emp->id, 'nome' => 'Dev', 'salario_base' => 1]);
        $colab = Colaborador::create(['empresa_id' => $emp->id, 'setor_id' => $setor->id, 'cargo_id' => $cargo->id, 'nome' => 'Ana', 'status' => 'ATIVO']);
        $tipo = TipoOcorrencia::create(['empresa_id' => $emp->id, 'nome' => 'Atraso']);

        return Ocorrencia::create([
            'empresa_id' => $emp->id, 'colaborador_id' => $colab->id,
            'tipo_ocorrencia_id' => $tipo->id, 'data_ocorrencia' => '2024-01-01', 'descricao' => 'x',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(Usuario::where('email', 'admin@tallents.com.br')->firstOrFail());
    }

    public function test_comentario_e_historico(): void
    {
        $oc = $this->ocorrencia();

        $this->post(route('ocorrencias.comentarios.store', $oc), ['comentario' => 'Olha isso'])
            ->assertRedirect();

        $this->assertDatabaseHas('ocorrencias_comentarios', ['ocorrencia_id' => $oc->id, 'comentario' => 'Olha isso']);
        $this->assertDatabaseHas('ocorrencias_historico', ['ocorrencia_id' => $oc->id, 'acao' => 'Comentário adicionado']);
    }

    public function test_upload_e_remocao_de_anexo(): void
    {
        Storage::fake('public');
        $oc = $this->ocorrencia();

        $this->post(route('ocorrencias.anexos.store', $oc), [
            'arquivo' => UploadedFile::fake()->create('atestado.pdf', 100, 'application/pdf'),
        ])->assertRedirect();

        $anexo = $oc->anexos()->first();
        $this->assertNotNull($anexo);
        Storage::disk('public')->assertExists($anexo->caminho);
        $this->assertDatabaseHas('ocorrencias_historico', ['ocorrencia_id' => $oc->id, 'acao' => 'Anexo adicionado']);

        $this->delete(route('ocorrencias.anexos.destroy', $anexo))->assertRedirect();
        Storage::disk('public')->assertMissing($anexo->caminho);
        $this->assertDatabaseMissing('ocorrencias_anexos', ['id' => $anexo->id]);
    }

    public function test_show_renderiza_com_secoes(): void
    {
        $oc = $this->ocorrencia();
        $this->get(route('ocorrencias.show', $oc))->assertOk()
            ->assertSee('Anexos')->assertSee('Comentários')->assertSee('Histórico');
    }
}
