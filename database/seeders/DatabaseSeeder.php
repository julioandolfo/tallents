<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        foreach (['ADMIN', 'RH', 'GESTOR', 'COLABORADOR'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Empresa padrão
        $empresa = Empresa::firstOrCreate(
            ['cnpj' => '00.000.000/0001-00'],
            [
                'nome'    => 'Tallents Tecnologia',
                'cidade'  => 'São Paulo',
                'estado'  => 'SP',
                'ativa'   => true,
            ]
        );

        // Usuário admin
        $admin = Usuario::firstOrCreate(
            ['email' => 'admin@tallents.com.br'],
            [
                'name'       => 'Administrador',
                'password'   => Hash::make('Tallents@2024'),
                'role'       => 'ADMIN',
                'empresa_id' => $empresa->id,
                'ativo'      => true,
            ]
        );
        $admin->assignRole('ADMIN');
    }
}
