<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ColaboradorController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\SetorController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\OcorrenciaController;
use App\Http\Controllers\HoraExtraController;
use App\Http\Controllers\PromocaoController;
use App\Http\Controllers\FechamentoPagamentoController;
use App\Http\Controllers\TipoBonusController;
use App\Http\Controllers\TipoOcorrenciaController;
use App\Http\Controllers\NivelHierarquicoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ConfiguracaoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Portal\PortalController;
use App\Http\Controllers\AdvertenciaController;
use App\Http\Controllers\BancoHorasController;
use App\Http\Controllers\DemissaoController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\DependenteController;
use App\Http\Controllers\FormacaoController;
use App\Http\Controllers\ComunicadoController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\VagaController;
use App\Http\Controllers\CandidatoController;
use App\Http\Controllers\AvaliacaoController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\PdiController;
use App\Http\Controllers\RecompensaController;
use App\Http\Controllers\PontoController;
use App\Http\Controllers\ResgateController;
use App\Http\Controllers\Portal\LojaController;
use App\Http\Controllers\IntegracaoController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\Portal\ContratoPortalController;
use App\Http\Controllers\QuadroController;
use App\Http\Controllers\TarefaController;

Route::get('/', fn() => auth()->check()
    ? redirect()->route(auth()->user()->painelRoute())
    : redirect()->route('login'));

// Auth
Route::get('/login', [LoginController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ─── Portal do Colaborador (autoatendimento) ─────────────────────────────────
Route::middleware(['auth'])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/', [PortalController::class, 'index'])->name('index');
    Route::get('/mural', [PortalController::class, 'mural'])->name('mural');
    Route::get('/loja', [LojaController::class, 'index'])->name('loja');
    Route::post('/loja/{recompensa}/resgatar', [LojaController::class, 'resgatar'])->name('loja.resgatar');
    Route::get('/contratos', [ContratoPortalController::class, 'index'])->name('contratos.index');
    Route::get('/contratos/{contrato}', [ContratoPortalController::class, 'show'])->name('contratos.show');
    Route::post('/contratos/{contrato}/assinar', [ContratoPortalController::class, 'assinar'])->name('contratos.assinar');
});

// ─── Área administrativa (RH / Admin / Gestor) ───────────────────────────────
Route::middleware(['auth', 'papel:ADMIN,RH,GESTOR'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Os parâmetros explícitos garantem que o nome do parâmetro da rota
    // (singularização) coincida com a variável tipada no controller, para o
    // route-model binding implícito funcionar em show/edit/update/destroy.
    Route::resource('colaboradores', ColaboradorController::class)->parameters(['colaboradores' => 'colaborador']);
    // Dependentes e formações (geridos na ficha do colaborador)
    Route::post('colaboradores/{colaborador}/dependentes', [DependenteController::class, 'store'])->name('dependentes.store');
    Route::delete('dependentes/{dependente}', [DependenteController::class, 'destroy'])->name('dependentes.destroy');
    Route::post('colaboradores/{colaborador}/formacoes', [FormacaoController::class, 'store'])->name('formacoes.store');
    Route::delete('formacoes/{formacao}', [FormacaoController::class, 'destroy'])->name('formacoes.destroy');
    Route::resource('empresas', EmpresaController::class);
    Route::resource('setores', SetorController::class)->parameters(['setores' => 'setor']);
    Route::resource('cargos', CargoController::class);
    Route::resource('ocorrencias', OcorrenciaController::class);
    Route::post('ocorrencias/{ocorrencia}/comentarios', [OcorrenciaController::class, 'adicionarComentario'])->name('ocorrencias.comentarios.store');
    Route::delete('ocorrencias/comentarios/{comentario}', [OcorrenciaController::class, 'removerComentario'])->name('ocorrencias.comentarios.destroy');
    Route::post('ocorrencias/{ocorrencia}/anexos', [OcorrenciaController::class, 'adicionarAnexo'])->name('ocorrencias.anexos.store');
    Route::delete('ocorrencias/anexos/{anexo}', [OcorrenciaController::class, 'removerAnexo'])->name('ocorrencias.anexos.destroy');
    Route::resource('advertencias', AdvertenciaController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::get('banco-horas', [BancoHorasController::class, 'index'])->name('banco-horas.index');
    Route::get('banco-horas/{colaborador}', [BancoHorasController::class, 'extrato'])->name('banco-horas.extrato');
    Route::post('banco-horas/{colaborador}', [BancoHorasController::class, 'lancar'])->name('banco-horas.lancar');
    Route::delete('banco-horas/movimentacoes/{movimentacao}', [BancoHorasController::class, 'remover'])->name('banco-horas.movimentacoes.destroy');
    Route::resource('horas-extras', HoraExtraController::class)->parameters(['horas-extras' => 'horasExtra']);
    Route::resource('promocoes', PromocaoController::class)->only(['index', 'create', 'store', 'show', 'destroy'])->parameters(['promocoes' => 'promocao']);

    // Ciclo de vida: desligamentos e onboarding
    Route::resource('demissoes', DemissaoController::class)->only(['index', 'create', 'store', 'show', 'destroy'])->parameters(['demissoes' => 'demissao']);
    Route::resource('onboarding', OnboardingController::class)->only(['index', 'create', 'store', 'show', 'update', 'destroy']);
    Route::post('onboarding/{onboarding}/tarefas', [OnboardingController::class, 'adicionarTarefa'])->name('onboarding.tarefas.store');
    Route::patch('onboarding/tarefas/{tarefa}', [OnboardingController::class, 'alternarTarefa'])->name('onboarding.tarefas.toggle');
    Route::delete('onboarding/tarefas/{tarefa}', [OnboardingController::class, 'removerTarefa'])->name('onboarding.tarefas.destroy');

    // Comunicação: comunicados (mural), eventos e documentos/manual
    Route::resource('comunicados', ComunicadoController::class);
    Route::resource('eventos', EventoController::class)->except(['show']);
    Route::resource('documentos', DocumentoController::class)->except(['show']);

    // Recrutamento (ATS): vagas e candidatos (funil/kanban)
    Route::resource('vagas', VagaController::class);
    Route::post('vagas/{vaga}/candidatos', [CandidatoController::class, 'store'])->name('candidatos.store');
    Route::patch('candidatos/{candidato}/mover', [CandidatoController::class, 'mover'])->name('candidatos.mover');
    Route::patch('candidatos/{candidato}', [CandidatoController::class, 'update'])->name('candidatos.update');
    Route::delete('candidatos/{candidato}', [CandidatoController::class, 'destroy'])->name('candidatos.destroy');

    // Desempenho: avaliações, feedbacks e PDI
    Route::resource('avaliacoes', AvaliacaoController::class)->parameters(['avaliacoes' => 'avaliacao']);
    Route::resource('feedbacks', FeedbackController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::resource('pdis', PdiController::class)->only(['index', 'create', 'store', 'show', 'update', 'destroy']);
    Route::post('pdis/{pdi}/acoes', [PdiController::class, 'adicionarAcao'])->name('pdis.acoes.store');
    Route::patch('pdis/acoes/{acao}', [PdiController::class, 'alternarAcao'])->name('pdis.acoes.toggle');
    Route::delete('pdis/acoes/{acao}', [PdiController::class, 'removerAcao'])->name('pdis.acoes.destroy');

    // Gamificação: recompensas, pontos e resgates
    Route::resource('recompensas', RecompensaController::class)->except(['show']);
    Route::get('pontos', [PontoController::class, 'index'])->name('pontos.index');
    Route::get('pontos/{colaborador}', [PontoController::class, 'extrato'])->name('pontos.extrato');
    Route::post('pontos/{colaborador}', [PontoController::class, 'lancar'])->name('pontos.lancar');
    Route::delete('pontos/movimentacoes/{movimentacao}', [PontoController::class, 'remover'])->name('pontos.movimentacoes.destroy');
    Route::get('resgates', [ResgateController::class, 'index'])->name('resgates.index');
    Route::patch('resgates/{resgate}', [ResgateController::class, 'update'])->name('resgates.update');

    // Contratos e assinatura digital (gestão)
    Route::resource('contratos', ContratoController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::patch('contratos/{contrato}/status', [ContratoController::class, 'status'])->name('contratos.status');

    // Quadros Kanban de tarefas
    Route::resource('quadros', QuadroController::class)->only(['index', 'store', 'show', 'destroy']);
    Route::post('quadros/{quadro}/tarefas', [TarefaController::class, 'store'])->name('tarefas.store');
    Route::patch('tarefas/{tarefa}/mover', [TarefaController::class, 'mover'])->name('tarefas.mover');
    Route::patch('tarefas/{tarefa}', [TarefaController::class, 'update'])->name('tarefas.update');
    Route::delete('tarefas/{tarefa}', [TarefaController::class, 'destroy'])->name('tarefas.destroy');
    Route::resource('fechamentos', FechamentoPagamentoController::class);
    Route::post('fechamentos/{fechamento}/fechar', [FechamentoPagamentoController::class, 'fechar'])->name('fechamentos.fechar');
    Route::resource('tipos-bonus', TipoBonusController::class)->parameters(['tipos-bonus' => 'tiposBonus']);
    Route::resource('tipos-ocorrencias', TipoOcorrenciaController::class)->parameters(['tipos-ocorrencias' => 'tiposOcorrencia']);
    Route::resource('niveis-hierarquicos', NivelHierarquicoController::class)->parameters(['niveis-hierarquicos' => 'niveisHierarquico']);
    Route::resource('usuarios', UsuarioController::class);

    Route::get('/integracoes', [IntegracaoController::class, 'index'])->name('integracoes.index');
    Route::put('/integracoes/{provider}', [IntegracaoController::class, 'update'])->name('integracoes.update');

    Route::get('/configuracoes', [ConfiguracaoController::class, 'index'])->name('configuracoes.index');
    Route::put('/configuracoes', [ConfiguracaoController::class, 'update'])->name('configuracoes.update');
    Route::put('/configuracoes/email', [ConfiguracaoController::class, 'updateEmail'])->name('configuracoes.email');
    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil.index');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
    Route::put('/perfil/senha', [PerfilController::class, 'updateSenha'])->name('perfil.senha');
    Route::put('/perfil/password', [PerfilController::class, 'updateSenha'])->name('perfil.password');
    Route::delete('/perfil', [PerfilController::class, 'destroy'])->name('perfil.destroy');
});
