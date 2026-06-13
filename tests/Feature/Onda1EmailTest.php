<?php

namespace Tests\Feature;

use App\Mail\ColaboradorBoasVindas;
use App\Mail\EmailTeste;
use App\Mail\OcorrenciaRegistrada;
use App\Mail\PromocaoRegistrada;
use App\Models\Cargo;
use App\Models\Colaborador;
use App\Models\ConfiguracaoEmail;
use App\Models\Empresa;
use App\Models\Setor;
use App\Models\TipoOcorrencia;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class Onda1EmailTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $emp;
    private Cargo $cargo;
    private Setor $setor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(Usuario::where('email', 'admin@tallents.com.br')->firstOrFail());

        // SMTP configurado para habilitar o envio.
        ConfiguracaoEmail::create([
            'driver' => 'smtp', 'smtp_host' => 'smtp.exemplo.com', 'smtp_port' => 587,
            'smtp_username' => 'u', 'smtp_password' => 'p', 'smtp_encryption' => 'tls',
            'from_email' => 'rh@tallents.com.br', 'from_name' => 'RH',
        ]);

        $this->emp = Empresa::firstOrFail();
        $this->setor = Setor::create(['empresa_id' => $this->emp->id, 'nome' => 'TI']);
        $this->cargo = Cargo::create(['empresa_id' => $this->emp->id, 'nome' => 'Dev', 'salario_base' => 1000]);
    }

    public function test_boas_vindas_ao_cadastrar_colaborador(): void
    {
        Mail::fake();

        $this->post(route('colaboradores.store'), [
            'nome' => 'Ana', 'empresa_id' => $this->emp->id, 'cargo_id' => $this->cargo->id,
            'setor_id' => $this->setor->id, 'status' => 'ATIVO', 'email_pessoal' => 'ana@gmail.com',
        ])->assertRedirect();

        Mail::assertSent(ColaboradorBoasVindas::class);
    }

    public function test_email_ao_registrar_ocorrencia_com_notificacao(): void
    {
        Mail::fake();
        $colab = Colaborador::create(['empresa_id' => $this->emp->id, 'setor_id' => $this->setor->id, 'cargo_id' => $this->cargo->id, 'nome' => 'Ana', 'status' => 'ATIVO', 'email_pessoal' => 'ana@gmail.com']);
        $tipo = TipoOcorrencia::create(['empresa_id' => $this->emp->id, 'nome' => 'Atraso', 'ativo' => true]);

        $this->post(route('ocorrencias.store'), [
            'colaborador_id' => $colab->id, 'tipo_ocorrencia_id' => $tipo->id,
            'data' => '2024-02-01', 'notificar_colaborador' => '1',
        ])->assertRedirect();

        Mail::assertSent(OcorrenciaRegistrada::class);
    }

    public function test_nao_envia_ocorrencia_sem_notificacao(): void
    {
        Mail::fake();
        $colab = Colaborador::create(['empresa_id' => $this->emp->id, 'setor_id' => $this->setor->id, 'cargo_id' => $this->cargo->id, 'nome' => 'Ana', 'status' => 'ATIVO', 'email_pessoal' => 'ana@gmail.com']);
        $tipo = TipoOcorrencia::create(['empresa_id' => $this->emp->id, 'nome' => 'Atraso', 'ativo' => true]);

        $this->post(route('ocorrencias.store'), [
            'colaborador_id' => $colab->id, 'tipo_ocorrencia_id' => $tipo->id, 'data' => '2024-02-01',
        ])->assertRedirect();

        Mail::assertNothingSent();
    }

    public function test_email_ao_promover(): void
    {
        Mail::fake();
        $colab = Colaborador::create(['empresa_id' => $this->emp->id, 'setor_id' => $this->setor->id, 'cargo_id' => $this->cargo->id, 'nome' => 'Ana', 'status' => 'ATIVO', 'email_pessoal' => 'ana@gmail.com', 'salario' => 1000]);
        $novo = Cargo::create(['empresa_id' => $this->emp->id, 'nome' => 'Senior', 'salario_base' => 2000]);

        $this->post(route('promocoes.store'), [
            'colaborador_id' => $colab->id, 'novo_cargo_id' => $novo->id, 'data' => '2024-03-01', 'novo_salario' => 2000,
        ])->assertRedirect();

        Mail::assertSent(PromocaoRegistrada::class);
    }

    public function test_testar_envio(): void
    {
        Mail::fake();
        $this->post(route('configuracoes.email.testar'), ['email_teste' => 'teste@gmail.com'])
            ->assertRedirect();
        Mail::assertSent(EmailTeste::class);
    }

    public function test_nao_envia_quando_smtp_nao_configurado(): void
    {
        ConfiguracaoEmail::query()->delete();
        Mail::fake();
        $colab = Colaborador::create(['empresa_id' => $this->emp->id, 'cargo_id' => $this->cargo->id, 'nome' => 'Ana', 'status' => 'ATIVO', 'email_pessoal' => 'ana@gmail.com']);
        $tipo = TipoOcorrencia::create(['empresa_id' => $this->emp->id, 'nome' => 'X', 'ativo' => true]);

        $this->post(route('ocorrencias.store'), [
            'colaborador_id' => $colab->id, 'tipo_ocorrencia_id' => $tipo->id, 'data' => '2024-02-01', 'notificar_colaborador' => '1',
        ])->assertRedirect();

        Mail::assertNothingSent();
    }
}
