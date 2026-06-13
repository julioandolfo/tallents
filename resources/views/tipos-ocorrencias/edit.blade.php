@extends('layouts.app')

@section('title', 'Editar Tipo de Ocorrência')
@section('page-title', 'Editar Tipo de Ocorrência')

@section('content')
<div class="py-4">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('tipos-ocorrencias.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h2 class="text-lg font-semibold text-gray-900">Editar Tipo: {{ $tipo->nome }}</h2>
    </div>

    <form method="POST" action="{{ route('tipos-ocorrencias.update', $tipo) }}">
        @csrf
        @method('PUT')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 form-grid">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                <input type="text" name="nome" value="{{ old('nome', $tipo->nome) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                    <select name="categoria" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Selecione</option>
                        @foreach(\App\Models\TipoOcorrencia::CATEGORIAS as $k => $v)
                            <option value="{{ $k }}" {{ old('categoria', $tipo->categoria) == $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gravidade</label>
                    <select name="gravidade" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Selecione</option>
                        @foreach(['leve' => 'Leve', 'media' => 'Média', 'grave' => 'Grave', 'gravissima' => 'Gravíssima'] as $val => $label)
                            <option value="{{ $val }}" {{ old('gravidade', $tipo->gravidade) == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                <textarea name="descricao" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('descricao', $tipo->descricao) }}</textarea>
            </div>
            <div class="border border-gray-100 rounded-lg p-3 bg-gray-50 space-y-2">
                <p class="text-xs font-semibold text-gray-500 uppercase">Campos de ponto/atraso ao registrar</p>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="hidden" name="permite_tempo_atraso" value="0">
                    <input type="checkbox" name="permite_tempo_atraso" value="1" {{ old('permite_tempo_atraso', $tipo->permite_tempo_atraso) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    Solicitar <strong>tempo de atraso</strong> (minutos)
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="hidden" name="permite_tipo_ponto" value="0">
                    <input type="checkbox" name="permite_tipo_ponto" value="1" {{ old('permite_tipo_ponto', $tipo->permite_tipo_ponto) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    Solicitar <strong>tipo de ponto</strong> (entrada/saída/intervalo)
                </label>
            </div>
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="hidden" name="ativo" value="0">
                <input type="checkbox" name="ativo" value="1" {{ old('ativo', $tipo->ativo) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                Tipo ativo
            </label>
        </div>
        <div class="flex items-center justify-end gap-3 mt-4">
            <a href="{{ route('tipos-ocorrencias.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">Cancelar</a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Salvar Alterações</button>
        </div>
    </form>
</div>
@endsection
