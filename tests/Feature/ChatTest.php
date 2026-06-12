<?php

namespace Tests\Feature;

use App\Models\Conversa;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;
    private Usuario $outro;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = Usuario::where('email', 'admin@tallents.com.br')->firstOrFail();
        $this->outro = Usuario::create(['name' => 'Bia', 'email' => 'bia@tallents.com.br', 'password' => bcrypt('x'), 'role' => 'RH']);
    }

    public function test_iniciar_conversa_e_enviar_mensagem(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('chat.iniciar', $this->outro))->assertRedirect();
        $conversa = Conversa::firstOrFail();
        $this->assertTrue($conversa->envolve($this->admin->id));

        $this->post(route('chat.enviar', $conversa), ['corpo' => 'Olá Bia!'])->assertRedirect();
        $this->assertDatabaseHas('mensagens', ['conversa_id' => $conversa->id, 'corpo' => 'Olá Bia!', 'remetente_id' => $this->admin->id]);
        $this->assertNotNull($conversa->fresh()->ultima_mensagem_em);
    }

    public function test_conversa_e_unica_entre_dois_usuarios(): void
    {
        $a = Conversa::entre($this->admin->id, $this->outro->id);
        $b = Conversa::entre($this->outro->id, $this->admin->id);

        $this->assertEquals($a->id, $b->id);
        $this->assertDatabaseCount('conversas', 1);
    }

    public function test_marca_mensagens_como_lidas_ao_abrir(): void
    {
        $this->actingAs($this->admin);
        $conversa = Conversa::entre($this->admin->id, $this->outro->id);
        $conversa->mensagens()->create(['remetente_id' => $this->outro->id, 'corpo' => 'oi', 'lida' => false]);

        $this->get(route('chat.show', $conversa))->assertOk();
        $this->assertEquals(0, $conversa->mensagens()->where('lida', false)->count());
    }

    public function test_usuario_externo_nao_acessa_conversa_alheia(): void
    {
        $conversa = Conversa::entre($this->admin->id, $this->outro->id);
        $intruso = Usuario::create(['name' => 'X', 'email' => 'x@tallents.com.br', 'password' => bcrypt('x'), 'role' => 'GESTOR']);

        $this->actingAs($intruso);
        $this->get(route('chat.show', $conversa))->assertForbidden();
    }

    public function test_pagina_index_renderiza(): void
    {
        $this->actingAs($this->admin);
        $this->get(route('chat.index'))->assertOk();
    }
}
