@extends('layouts.app')

@section('title', $comunicado->titulo)
@section('page-title', 'Comunicado')

@section('content')
<div class="space-y-4 py-4 max-w-3xl">
    <div class="flex items-center justify-between">
        <a href="{{ route('comunicados.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <a href="{{ route('comunicados.edit', $comunicado) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Editar</a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-2 mb-3">
            <x-ui.badge :color="$comunicado->cor()">{{ $comunicado->categoriaLabel() }}</x-ui.badge>
            @if($comunicado->destaque)<x-ui.badge color="indigo">Destaque</x-ui.badge>@endif
            @if(!$comunicado->publicado)<x-ui.badge color="gray">Rascunho</x-ui.badge>@endif
        </div>
        <h1 class="text-xl font-bold text-gray-900">{{ $comunicado->titulo }}</h1>
        <p class="mt-1 text-xs text-gray-400">
            {{ optional($comunicado->autor)->name ?? 'Sistema' }} ·
            {{ optional($comunicado->publicado_em ?? $comunicado->created_at)->format('d/m/Y H:i') }}
            {{ $comunicado->empresa ? '· ' . $comunicado->empresa->nome : '· Todas as empresas' }}
        </p>
        <div class="mt-5 text-sm text-gray-700 whitespace-pre-line leading-relaxed">{{ $comunicado->conteudo }}</div>
    </div>
</div>
@endsection
