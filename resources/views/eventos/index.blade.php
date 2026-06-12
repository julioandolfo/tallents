@extends('layouts.app')

@section('title', 'Eventos')
@section('page-title', 'Agenda de Eventos')

@section('content')
<div class="space-y-4 py-4">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $eventos->total() }} evento(s) {{ request('passados') ? '' : 'a partir de hoje' }}</p>
        <div class="flex items-center gap-2">
            <a href="{{ route('eventos.index', ['passados' => request('passados') ? null : 1]) }}"
               class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">
                {{ request('passados') ? 'Só próximos' : 'Ver todos' }}
            </a>
            <a href="{{ route('eventos.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Novo Evento
            </a>
        </div>
    </div>

    <div class="space-y-3">
        @forelse($eventos as $ev)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-start gap-4">
                <div class="flex-shrink-0 w-14 text-center">
                    <div class="text-2xl font-bold text-gray-900 leading-none">{{ $ev->inicio->format('d') }}</div>
                    <div class="text-xs uppercase text-gray-500">{{ $ev->inicio->translatedFormat('M') }}</div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <x-ui.badge :color="$ev->cor()">{{ $ev->tipoLabel() }}</x-ui.badge>
                        <span class="text-xs text-gray-400">{{ $ev->inicio->format('H:i') }}{{ $ev->fim ? ' – ' . $ev->fim->format('H:i') : '' }}</span>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">{{ $ev->titulo }}</h3>
                    @if($ev->local)<p class="text-sm text-gray-500">📍 {{ $ev->local }}</p>@endif
                    @if($ev->descricao)<p class="mt-1 text-sm text-gray-600 line-clamp-2">{{ $ev->descricao }}</p>@endif
                    <p class="mt-1 text-xs text-gray-400">{{ $ev->empresa ? $ev->empresa->nome : 'Todas as empresas' }}</p>
                </div>
                <div class="flex items-center gap-1">
                    <a href="{{ route('eventos.edit', $ev) }}" class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded transition">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                    <form method="POST" action="{{ route('eventos.destroy', $ev) }}" x-data @submit.prevent="if(confirm('Excluir evento?')) $el.submit()">
                        @csrf @method('DELETE')
                        <button class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-12 text-center text-sm text-gray-400">Nenhum evento</div>
        @endforelse
    </div>

    @if($eventos->hasPages())
        <div>{{ $eventos->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
