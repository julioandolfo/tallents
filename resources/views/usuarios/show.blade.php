@extends('layouts.app')

@section('title', $usuario->name ?? 'Usuário')
@section('page-title', 'Detalhes do Usuário')

@section('content')
<div class="space-y-6 py-4">

    <div class="flex items-center gap-3">
        <a href="{{ route('usuarios.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div class="flex-1"></div>
        <a href="{{ route('usuarios.edit', $usuario) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
            Editar
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-xl font-bold text-gray-900">{{ $usuario->name }}</h2>
        <p class="text-sm text-gray-500">{{ $usuario->email }}</p>

        <dl class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500">Perfil</dt><dd class="text-gray-900">{{ ucfirst($usuario->perfil) }}</dd></div>
            <div><dt class="text-gray-500">Situação</dt><dd class="text-gray-900">{{ $usuario->ativo ? 'Ativo' : 'Inativo' }}</dd></div>
            <div><dt class="text-gray-500">Último acesso</dt><dd class="text-gray-900">{{ $usuario->last_login_at ? $usuario->last_login_at->diffForHumans() : 'Nunca' }}</dd></div>
            <div><dt class="text-gray-500">Empresas</dt><dd class="text-gray-900">{{ $usuario->empresas->pluck('nome')->join(', ') ?: '—' }}</dd></div>
        </dl>
    </div>
</div>
@endsection
