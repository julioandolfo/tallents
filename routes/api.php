<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ColaboradorApiController;
use App\Http\Controllers\Api\SetorApiController;
use App\Http\Controllers\Api\CargoApiController;
use App\Http\Controllers\Api\BonusApiController;
use App\Http\Controllers\Api\CnpjApiController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/colaboradores', [ColaboradorApiController::class, 'index']);
    Route::get('/setores', [SetorApiController::class, 'index']);
    Route::get('/cargos', [CargoApiController::class, 'index']);
    Route::get('/lideres', [ColaboradorApiController::class, 'lideres']);
    Route::get('/bonus/{colaborador}', [BonusApiController::class, 'show']);
    Route::post('/bonus', [BonusApiController::class, 'store']);
    Route::delete('/bonus/{bonus}', [BonusApiController::class, 'destroy']);
    Route::get('/cnpj/{cnpj}', [CnpjApiController::class, 'show']);
});
