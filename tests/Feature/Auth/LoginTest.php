<?php

namespace Tests\Feature\Auth;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_tela_de_login_renderiza(): void
    {
        $this->get(route('login'))->assertOk();
    }

    public function test_usuario_ativo_autentica_com_email_e_senha(): void
    {
        $usuario = Usuario::create([
            'name'     => 'Teste',
            'email'    => 'teste@tallents.com.br',
            'password' => Hash::make('segredo123'),
            'role'     => 'ADMIN',
            'ativo'    => true,
        ]);

        $res = $this->post(route('login'), [
            'email'    => 'teste@tallents.com.br',
            'password' => 'segredo123',
        ]);

        $res->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($usuario);
    }

    public function test_credenciais_invalidas_nao_autenticam(): void
    {
        Usuario::create([
            'name'     => 'Teste',
            'email'    => 'teste@tallents.com.br',
            'password' => Hash::make('segredo123'),
            'role'     => 'ADMIN',
            'ativo'    => true,
        ]);

        $this->post(route('login'), [
            'email'    => 'teste@tallents.com.br',
            'password' => 'senha-errada',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_usuario_inativo_nao_autentica(): void
    {
        Usuario::create([
            'name'     => 'Inativo',
            'email'    => 'inativo@tallents.com.br',
            'password' => Hash::make('segredo123'),
            'role'     => 'ADMIN',
            'ativo'    => false,
        ]);

        $this->post(route('login'), [
            'email'    => 'inativo@tallents.com.br',
            'password' => 'segredo123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_logout(): void
    {
        $usuario = Usuario::create([
            'name'     => 'Teste',
            'email'    => 'teste@tallents.com.br',
            'password' => Hash::make('segredo123'),
            'role'     => 'ADMIN',
            'ativo'    => true,
        ]);

        $this->actingAs($usuario->fresh())->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
