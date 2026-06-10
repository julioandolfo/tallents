<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_painel_filament_renderiza(): void
    {
        $this->seed();
        $admin = Usuario::where('email', 'admin@tallents.com.br')->firstOrFail();

        // login do painel acessível sem autenticação
        $this->get('/admin/login')->assertSuccessful();

        $this->actingAs($admin);

        $rotas = [
            '/admin',
            '/admin/empresas',
            '/admin/colaboradors',
            '/admin/cargos',
            '/admin/setors',
            '/admin/ocorrencias',
            '/admin/hora-extras',
            '/admin/usuarios',
        ];
        $falhas = [];
        foreach ($rotas as $url) {
            $code = $this->get($url)->getStatusCode();
            if ($code >= 400) {
                $falhas[] = "$url => $code";
            }
        }
        $this->assertEmpty($falhas, "Páginas do painel com erro:\n" . implode("\n", $falhas));
    }
}
