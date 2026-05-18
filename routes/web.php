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

Route::get('/', fn() => redirect()->route('dashboard'));

// Auth
Route::get('/login', [LoginController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Autenticado
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('colaboradores', ColaboradorController::class);
    Route::resource('empresas', EmpresaController::class);
    Route::resource('setores', SetorController::class);
    Route::resource('cargos', CargoController::class);
    Route::resource('ocorrencias', OcorrenciaController::class);
    Route::resource('horas-extras', HoraExtraController::class);
    Route::resource('promocoes', PromocaoController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('fechamentos', FechamentoPagamentoController::class);
    Route::post('fechamentos/{fechamento}/fechar', [FechamentoPagamentoController::class, 'fechar'])->name('fechamentos.fechar');
    Route::resource('tipos-bonus', TipoBonusController::class);
    Route::resource('tipos-ocorrencias', TipoOcorrenciaController::class);
    Route::resource('niveis-hierarquicos', NivelHierarquicoController::class);
    Route::resource('usuarios', UsuarioController::class);

    Route::get('/configuracoes', [ConfiguracaoController::class, 'index'])->name('configuracoes.index');
    Route::put('/configuracoes/email', [ConfiguracaoController::class, 'updateEmail'])->name('configuracoes.email');
    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil.index');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
    Route::put('/perfil/senha', [PerfilController::class, 'updateSenha'])->name('perfil.senha');
});
