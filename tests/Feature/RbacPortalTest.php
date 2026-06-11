<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RbacPortalTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): Usuario
    {
        return Usuario::create([
            'name' => $role,
            'email' => strtolower($role) . '@t.com',
            'password' => Hash::make('segredo123'),
            'role' => $role,
            'ativo' => true,
        ])->fresh();
    }

    public function test_admin_acessa_area_administrativa(): void
    {
        $this->seed();
        $admin = Usuario::where('email', 'admin@tallents.com.br')->firstOrFail();
        $this->actingAs($admin);

        $this->get(route('dashboard'))->assertOk();
        $this->get(route('colaboradores.index'))->assertOk();
    }

    public function test_colaborador_e_barrado_da_area_admin_e_vai_ao_portal(): void
    {
        $colab = $this->makeUser('COLABORADOR');
        $this->actingAs($colab);

        // área admin → redireciona ao portal
        $this->get(route('dashboard'))->assertRedirect(route('portal.index'));
        $this->get(route('colaboradores.index'))->assertRedirect(route('portal.index'));

        // portal acessível
        $this->get(route('portal.index'))->assertOk();
    }

    public function test_login_redireciona_por_papel(): void
    {
        $admin = $this->makeUser('ADMIN');
        $this->post(route('login'), ['email' => $admin->email, 'password' => 'segredo123'])
            ->assertRedirect(route('dashboard'));
        $this->post(route('logout'));

        $colab = $this->makeUser('COLABORADOR');
        $this->post(route('login'), ['email' => $colab->email, 'password' => 'segredo123'])
            ->assertRedirect(route('portal.index'));
    }
}
