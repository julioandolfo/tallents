@extends('layouts.app')

@section('title', $empresa->nome ?? 'Empresa')
@section('page-title', 'Detalhes da Empresa')

@section('content')
<div class="space-y-6 py-4">

    <div class="flex items-center gap-3">
        <a href="{{ route('empresas.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div class="flex-1"></div>
        <a href="{{ route('empresas.edit', $empresa) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
            Editar
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-xl font-bold text-gray-900">{{ $empresa->nome }}</h2>
        @if($empresa->razao_social)
            <p class="text-sm text-gray-500">{{ $empresa->razao_social }}</p>
        @endif

        <dl class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500">CNPJ</dt><dd class="text-gray-900">{{ $empresa->cnpj ?: '—' }}</dd></div>
            <div><dt class="text-gray-500">E-mail</dt><dd class="text-gray-900">{{ $empresa->email ?: '—' }}</dd></div>
            <div><dt class="text-gray-500">Telefone</dt><dd class="text-gray-900">{{ $empresa->telefone ?: '—' }}</dd></div>
            <div><dt class="text-gray-500">Site</dt><dd class="text-gray-900">{{ $empresa->site ?: '—' }}</dd></div>
            <div><dt class="text-gray-500">Cidade/UF</dt><dd class="text-gray-900">{{ trim(($empresa->cidade ?: '—') . ' / ' . ($empresa->estado ?: '—'), ' /') }}</dd></div>
            <div><dt class="text-gray-500">% Hora Extra</dt><dd class="text-gray-900">{{ $empresa->percentual_hora_extra }}%</dd></div>
            <div><dt class="text-gray-500">Situação</dt><dd class="text-gray-900">{{ $empresa->ativa ? 'Ativa' : 'Inativa' }}</dd></div>
        </dl>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Colaboradores</p>
            <p class="text-2xl font-bold text-gray-900">{{ $empresa->colaboradores_count ?? $empresa->colaboradores()->count() }}</p>
            <p class="text-xs text-gray-400">{{ $colaboradoresAtivos ?? 0 }} ativos</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Setores</p>
            <p class="text-2xl font-bold text-gray-900">{{ $empresa->setores_count ?? $empresa->setores()->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-sm text-gray-500">Cargos</p>
            <p class="text-2xl font-bold text-gray-900">{{ $empresa->cargos_count ?? $empresa->cargos()->count() }}</p>
        </div>
    </div>
</div>
@endsection
