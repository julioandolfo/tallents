<?php

use Illuminate\Support\Facades\Route;

/*
| O sistema é o painel administrativo Filament, servido em /admin.
| As telas antigas (Blade) foram descontinuadas.
*/

Route::get('/', fn () => redirect('/admin'));

// Mantém o nome de rota "login" (usado por middlewares de autenticação)
// apontando para o login do painel.
Route::get('/login', fn () => redirect('/admin/login'))->name('login');
