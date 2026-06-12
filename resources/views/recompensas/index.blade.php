@extends('layouts.app')

@section('title', 'Recompensas')
@section('page-title', 'Loja — Recompensas')

@section('content')
<div class="space-y-4 py-4">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $recompensas->total() }} recompensa(s)</p>
        <a href="{{ route('recompensas.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nova Recompensa
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($recompensas as $rec)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden {{ $rec->ativa ? '' : 'opacity-60' }}">
                @if($rec->imagemUrl())
                    <img src="{{ $rec->imagemUrl() }}" class="h-32 w-full object-cover" alt="{{ $rec->nome }}">
                @else
                    <div class="h-32 w-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center">
                        <svg class="h-10 w-10 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    </div>
                @endif
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900">{{ $rec->nome }}</h3>
                        <span class="text-sm font-bold text-indigo-600">{{ $rec->custo_pontos }} pts</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ is_null($rec->estoque) ? 'Estoque ilimitado' : $rec->estoque . ' em estoque' }}{{ $rec->ativa ? '' : ' · Inativa' }}</p>
                    <div class="mt-3 flex items-center justify-end gap-1">
                        <a href="{{ route('recompensas.edit', $rec) }}" class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded transition">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('recompensas.destroy', $rec) }}" x-data @submit.prevent="if(confirm('Excluir recompensa?')) $el.submit()">
                            @csrf @method('DELETE')
                            <button class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="sm:col-span-2 lg:col-span-3 bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-12 text-center text-sm text-gray-400">Nenhuma recompensa cadastrada</div>
        @endforelse
    </div>

    @if($recompensas->hasPages())
        <div>{{ $recompensas->links() }}</div>
    @endif
</div>
@endsection
