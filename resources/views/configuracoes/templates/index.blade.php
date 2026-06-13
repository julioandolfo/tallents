@extends('layouts.app')
@section('title', 'Templates de E-mail')
@section('page-title', 'Templates de E-mail')
@section('content')
<div class="py-4 max-w-4xl space-y-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('configuracoes.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h2 class="text-lg font-semibold text-gray-900">Templates de E-mail</h2>
    </div>
    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>@endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 divide-y divide-gray-100">
        @forelse($templates as $t)
            <div class="flex items-center justify-between px-6 py-4">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $t->nome }}</p>
                    <p class="text-xs text-gray-500">Assunto: {{ $t->assunto }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <x-ui.badge :color="$t->ativo ? 'green' : 'gray'">{{ $t->ativo ? 'Ativo' : 'Inativo' }}</x-ui.badge>
                    <a href="{{ route('configuracoes.templates.edit', $t) }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Editar</a>
                </div>
            </div>
        @empty
            <p class="px-6 py-8 text-center text-sm text-gray-400">Nenhum template cadastrado.</p>
        @endforelse
    </div>
</div>
@endsection
