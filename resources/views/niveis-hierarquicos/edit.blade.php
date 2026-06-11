@extends('layouts.app')

@section('title', 'Editar Nível Hierárquico')
@section('page-title', 'Editar Nível Hierárquico')

@section('content')
<div class="py-4 max-w-xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('niveis-hierarquicos.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h2 class="text-lg font-semibold text-gray-900">Editar Nível: {{ $nivel->nome }}</h2>
    </div>

    <form method="POST" action="{{ route('niveis-hierarquicos.update', $nivel) }}">
        @csrf
        @method('PUT')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                <input type="text" name="nome" value="{{ old('nome', $nivel->nome) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ordem</label>
                <input type="number" name="ordem" value="{{ old('ordem', $nivel->ordem) }}" min="1"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                <textarea name="descricao" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('descricao', $nivel->descricao) }}</textarea>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3 mt-4">
            <a href="{{ route('niveis-hierarquicos.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">Cancelar</a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Salvar Alterações</button>
        </div>
    </form>
</div>
@endsection
