@extends('layouts.app')

@section('title', 'Quadros')
@section('page-title', 'Quadros de Tarefas')

@section('content')
<div class="space-y-4 py-4" x-data="{ novo: false }">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $quadros->count() }} quadro(s)</p>
        <button @click="novo = !novo" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Novo Quadro
        </button>
    </div>

    <form x-show="novo" x-cloak method="POST" action="{{ route('quadros.store') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 grid grid-cols-1 sm:grid-cols-3 gap-3">
        @csrf
        <input type="text" name="nome" required placeholder="Nome do quadro *" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <input type="text" name="descricao" placeholder="Descrição" class="sm:col-span-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Criar</button>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($quadros as $q)
            <a href="{{ route('quadros.show', $q) }}" class="block bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:border-indigo-300 hover:shadow transition">
                <h3 class="text-base font-semibold text-gray-900">{{ $q->nome }}</h3>
                @if($q->descricao)<p class="text-sm text-gray-500 mt-1">{{ \Illuminate\Support\Str::limit($q->descricao, 80) }}</p>@endif
                <p class="mt-3 text-xs text-gray-400">{{ $q->tarefas_count }} tarefa(s)</p>
            </a>
        @empty
            <div class="sm:col-span-2 lg:col-span-3 bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-12 text-center text-sm text-gray-400">Nenhum quadro. Crie o primeiro!</div>
        @endforelse
    </div>
</div>
@endsection
