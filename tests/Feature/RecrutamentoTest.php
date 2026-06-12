<?php

namespace Tests\Feature;

use App\Models\Candidato;
use App\Models\Empresa;
use App\Models\Usuario;
use App\Models\Vaga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecrutamentoTest extends TestCase
{
    use RefreshDatabase;

    private Vaga $vaga;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(Usuario::where('email', 'admin@tallents.com.br')->firstOrFail());

        $this->vaga = Vaga::create([
            'empresa_id' => Empresa::firstOrFail()->id,
            'titulo' => 'Dev PHP', 'modalidade' => 'REMOTO', 'quantidade' => 1, 'status' => 'ABERTA',
        ]);
    }

    public function test_criar_vaga(): void
    {
        $this->post(route('vagas.store'), [
            'titulo' => 'Designer', 'modalidade' => 'HIBRIDO', 'quantidade' => 2, 'status' => 'ABERTA',
        ])->assertRedirect(route('vagas.index'));

        $this->assertDatabaseHas('vagas', ['titulo' => 'Designer', 'modalidade' => 'HIBRIDO']);
    }

    public function test_adicionar_e_mover_candidato_no_funil(): void
    {
        $this->post(route('candidatos.store', $this->vaga), [
            'nome' => 'Carlos', 'email' => 'carlos@exemplo.com', 'nota' => 8,
        ])->assertRedirect();

        $cand = Candidato::firstOrFail();
        $this->assertEquals('TRIAGEM', $cand->etapa);

        $this->patch(route('candidatos.mover', $cand), ['etapa' => 'ENTREVISTA'])->assertRedirect();
        $this->assertEquals('ENTREVISTA', $cand->fresh()->etapa);
    }

    public function test_remover_candidato(): void
    {
        $cand = $this->vaga->candidatos()->create(['nome' => 'Ana']);
        $this->delete(route('candidatos.destroy', $cand))->assertRedirect(route('vagas.show', $this->vaga));
        $this->assertDatabaseMissing('candidatos', ['id' => $cand->id]);
    }

    public function test_paginas_renderizam(): void
    {
        $this->vaga->candidatos()->create(['nome' => 'Ana', 'etapa' => 'TESTE']);
        $this->get(route('vagas.index'))->assertOk();
        $this->get(route('vagas.create'))->assertOk();
        $this->get(route('vagas.show', $this->vaga))->assertOk()->assertSee('Ana');
        $this->get(route('vagas.edit', $this->vaga))->assertOk();
    }
}
