<?php

namespace Tests\Feature;

use App\Models\Cargo;
use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\NivelHierarquico;
use App\Models\Setor;
use App\Models\TipoBonus;
use App\Models\TipoOcorrencia;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Exercita os formulários de cadastro (store) de cada módulo, autenticado
 * como admin, garantindo que o fluxo form → controller → banco funciona
 * de ponta a ponta após a reconciliação migrations/controllers/views.
 */
class SmokeCrudTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;
    private Empresa $empresa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin   = Usuario::where('email', 'admin@tallents.com.br')->firstOrFail();
        $this->empresa = Empresa::firstOrFail();
        $this->actingAs($this->admin);
    }

    public function test_empresa_pode_ser_cadastrada(): void
    {
        $res = $this->post(route('empresas.store'), [
            'nome'        => 'Nova Empresa LTDA',
            'razao_social'=> 'Nova Empresa Comercial LTDA',
            'cnpj'        => '12.345.678/0001-90',
            'email'       => 'contato@nova.com.br',
            'telefone'    => '(11) 99999-0000',
            'cep'         => '01310-000',
            'logradouro'  => 'Av. Paulista',
            'numero'      => '1000',
            'cidade'      => 'São Paulo',
            'estado'      => 'SP',
        ]);
        $res->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('empresas', ['nome' => 'Nova Empresa LTDA']);
    }

    public function test_setor_pode_ser_cadastrado(): void
    {
        $res = $this->post(route('setores.store'), [
            'empresa_id' => $this->empresa->id,
            'nome'       => 'Tecnologia',
            'descricao'  => 'Setor de TI',
        ]);
        $res->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('setores', ['nome' => 'Tecnologia']);
    }

    public function test_nivel_hierarquico_pode_ser_cadastrado(): void
    {
        $res = $this->post(route('niveis-hierarquicos.store'), [
            'nome'      => 'Gerência',
            'ordem'     => 2,
            'descricao' => 'Nível gerencial',
        ]);
        $res->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('niveis_hierarquicos', ['nome' => 'Gerência', 'nivel' => 2]);
    }

    public function test_cargo_pode_ser_cadastrado(): void
    {
        $res = $this->post(route('cargos.store'), [
            'nome'         => 'Desenvolvedor',
            'descricao'    => 'Dev full stack',
            'salario_base' => 5000,
            'salario_maximo' => 12000,
        ]);
        $res->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('cargos', ['nome' => 'Desenvolvedor']);
    }

    public function test_tipo_ocorrencia_pode_ser_cadastrado(): void
    {
        $res = $this->post(route('tipos-ocorrencias.store'), [
            'nome'      => 'Atraso',
            'descricao' => 'Atraso na entrada',
            'gravidade' => 'leve',
            'ativo'     => 1,
        ]);
        $res->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('tipos_ocorrencias', ['nome' => 'Atraso']);
    }

    public function test_tipo_bonus_pode_ser_cadastrado(): void
    {
        $res = $this->post(route('tipos-bonus.store'), [
            'nome'         => 'Performance',
            'descricao'    => 'Bônus por metas',
            'tipo_calculo' => 'percentual',
            'percentual'   => 10,
        ]);
        $res->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('tipos_bonus', ['nome' => 'Performance']);
    }

    public function test_colaborador_pode_ser_cadastrado(): void
    {
        $setor = Setor::create(['empresa_id' => $this->empresa->id, 'nome' => 'TI']);
        $cargo = Cargo::create(['empresa_id' => $this->empresa->id, 'nome' => 'Dev', 'salario_base' => 5000]);

        $res = $this->post(route('colaboradores.store'), [
            'empresa_id'      => $this->empresa->id,
            'nome'            => 'João Silva',
            'cpf'             => '123.456.789-00',
            'email_pessoal'   => 'joao@pessoal.com',
            'email_login'     => 'joao@empresa.com',
            'regime_trabalho' => 'CLT',
            'status'          => 'ATIVO',
            'cargo_id'        => $cargo->id,
            'setor_id'        => $setor->id,
            'salario'         => 5000,
            'data_admissao'   => '2024-01-15',
        ]);
        $res->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('colaboradores', ['nome' => 'João Silva', 'tipo_contrato' => 'CLT']);
    }

    public function test_ocorrencia_hora_extra_e_promocao_podem_ser_cadastradas(): void
    {
        $setor = Setor::create(['empresa_id' => $this->empresa->id, 'nome' => 'TI']);
        $cargo = Cargo::create(['empresa_id' => $this->empresa->id, 'nome' => 'Dev', 'salario_base' => 5000]);
        $cargo2 = Cargo::create(['empresa_id' => $this->empresa->id, 'nome' => 'Tech Lead', 'salario_base' => 9000]);
        $tipo  = TipoOcorrencia::create(['empresa_id' => $this->empresa->id, 'nome' => 'Atraso']);
        $colab = Colaborador::create([
            'empresa_id' => $this->empresa->id,
            'setor_id'   => $setor->id,
            'cargo_id'   => $cargo->id,
            'nome'       => 'Maria',
            'salario'    => 5000,
            'status'     => 'ATIVO',
        ]);

        $this->post(route('ocorrencias.store'), [
            'colaborador_id'     => $colab->id,
            'tipo_ocorrencia_id' => $tipo->id,
            'data'               => '2024-02-01',
            'gravidade'          => 'media',
            'observacao'         => 'Chegou 30min atrasado',
        ])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('ocorrencias', ['colaborador_id' => $colab->id]);

        $this->post(route('horas-extras.store'), [
            'colaborador_id' => $colab->id,
            'data'           => '2024-02-02',
            'quantidade'     => 3,
            'motivo'         => 'Entrega de projeto',
            'status'         => 'pendente',
        ])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('horas_extras', ['colaborador_id' => $colab->id, 'horas' => 3]);

        $this->post(route('promocoes.store'), [
            'colaborador_id' => $colab->id,
            'novo_cargo_id'  => $cargo2->id,
            'data'           => '2024-03-01',
            'novo_salario'   => 9000,
            'motivo'         => 'Mérito',
        ])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('promocoes', ['colaborador_id' => $colab->id, 'salario_novo' => 9000]);
        $this->assertDatabaseHas('colaboradores', ['id' => $colab->id, 'cargo_id' => $cargo2->id, 'salario' => 9000]);
    }

    public function test_paginas_principais_renderizam(): void
    {
        // index + create de cada módulo devem responder 200 (views OK)
        $rotas = [
            'dashboard',
            'empresas.index', 'empresas.create',
            'setores.index', 'setores.create',
            'cargos.index', 'cargos.create',
            'niveis-hierarquicos.index', 'niveis-hierarquicos.create',
            'colaboradores.index', 'colaboradores.create',
            'tipos-ocorrencias.index', 'tipos-ocorrencias.create',
            'tipos-bonus.index', 'tipos-bonus.create',
            'ocorrencias.index', 'ocorrencias.create',
            'horas-extras.index', 'horas-extras.create',
            'promocoes.index', 'promocoes.create',
            'fechamentos.index', 'fechamentos.create',
            'usuarios.index', 'usuarios.create',
            'configuracoes.index',
            'perfil.index',
        ];

        $falhas = [];
        foreach ($rotas as $rota) {
            $status = $this->get(route($rota))->getStatusCode();
            if ($status !== 200) {
                $falhas[] = "$rota => $status";
            }
        }

        $this->assertEmpty($falhas, "Páginas com erro:\n" . implode("\n", $falhas));
    }

    public function test_paginas_show_e_edit_renderizam(): void
    {
        $setor = Setor::create(['empresa_id' => $this->empresa->id, 'nome' => 'TI']);
        $cargo = Cargo::create(['empresa_id' => $this->empresa->id, 'nome' => 'Dev', 'salario_base' => 5000]);
        $colab = Colaborador::create([
            'empresa_id' => $this->empresa->id,
            'setor_id'   => $setor->id,
            'cargo_id'   => $cargo->id,
            'nome'       => 'Ana',
            'salario'    => 5000,
            'status'     => 'ATIVO',
        ]);

        $tipoOc = TipoOcorrencia::create(['empresa_id' => $this->empresa->id, 'nome' => 'Atraso']);
        $cargo2 = Cargo::create(['empresa_id' => $this->empresa->id, 'nome' => 'Lead', 'salario_base' => 9000]);

        $oc = \App\Models\Ocorrencia::create([
            'empresa_id' => $this->empresa->id, 'colaborador_id' => $colab->id,
            'tipo_ocorrencia_id' => $tipoOc->id, 'data_ocorrencia' => '2024-01-01', 'descricao' => 'x',
        ]);
        $he = \App\Models\HoraExtra::create([
            'empresa_id' => $this->empresa->id, 'colaborador_id' => $colab->id,
            'data' => '2024-01-02', 'horas' => 2, 'percentual' => 50, 'valor' => 100, 'status' => 'pendente',
        ]);
        $promo = \App\Models\Promocao::create([
            'empresa_id' => $this->empresa->id, 'colaborador_id' => $colab->id,
            'cargo_novo_id' => $cargo2->id, 'salario_anterior' => 5000, 'salario_novo' => 9000,
            'data_promocao' => '2024-03-01',
        ]);

        $paginas = [
            route('empresas.show', $this->empresa),
            route('empresas.edit', $this->empresa),
            route('setores.show', $setor),   // redireciona para edit
            route('setores.edit', $setor),
            route('cargos.show', $cargo),     // redireciona para edit
            route('cargos.edit', $cargo),
            route('colaboradores.show', $colab),
            route('colaboradores.edit', $colab),
            route('ocorrencias.show', $oc),
            route('ocorrencias.edit', $oc),
            route('horas-extras.show', $he),
            route('horas-extras.edit', $he),
            route('promocoes.show', $promo),
            route('usuarios.show', $this->admin),
            route('usuarios.edit', $this->admin),
        ];

        $falhas = [];
        foreach ($paginas as $url) {
            // 200 (renderizou) ou 3xx (redirect válido, ex.: show → edit)
            $status = $this->get($url)->getStatusCode();
            if ($status >= 400) {
                $falhas[] = "$url => $status";
            }
        }

        $this->assertEmpty($falhas, "Páginas show/edit com erro:\n" . implode("\n", $falhas));
    }

    public function test_configuracoes_e_perfil_podem_ser_atualizados(): void
    {
        $this->put(route('configuracoes.update'), [
            'secao'             => 'email',
            'mail_driver'       => 'smtp',
            'mail_host'         => 'smtp.gmail.com',
            'mail_port'         => 587,
            'mail_username'     => 'user@gmail.com',
            'mail_encryption'   => 'tls',
            'mail_from_name'    => 'Tallents RH',
            'mail_from_address' => 'no-reply@tallents.com.br',
        ])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('configuracoes_email', ['smtp_host' => 'smtp.gmail.com']);

        $this->put(route('configuracoes.update'), [
            'secao'        => 'empresa',
            'empresa_nome' => 'Tallents Atualizada',
        ])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('empresas', ['nome' => 'Tallents Atualizada']);

        $this->put(route('perfil.update'), [
            'name'  => 'Admin Renomeado',
            'email' => $this->admin->email,
        ])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $this->admin->id, 'name' => 'Admin Renomeado']);
    }

    public function test_usuario_pode_ser_cadastrado(): void
    {
        $res = $this->post(route('usuarios.store'), [
            'name'                  => 'Novo Operador',
            'email'                 => 'operador@tallents.com.br',
            'password'              => 'senha12345',
            'password_confirmation' => 'senha12345',
            'perfil'                => 'rh',
        ]);
        $res->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'operador@tallents.com.br', 'role' => 'RH']);
    }
}
