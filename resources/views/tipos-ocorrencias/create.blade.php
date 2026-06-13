@extends('layouts.app')

@section('title', 'Novo Tipo de Ocorrência')
@section('page-title', 'Novo Tipo de Ocorrência')

@section('content')
<div class="py-4">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('tipos-ocorrencias.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h2 class="text-lg font-semibold text-gray-900">Novo Tipo de Ocorrência</h2>
    </div>

    <form method="POST" action="{{ route('tipos-ocorrencias.store') }}">
        @csrf
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 form-grid">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome <span class="text-red-500">*</span></label>
                <input type="text" name="nome" value="{{ old('nome') }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                    <select name="categoria" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Selecione</option>
                        @foreach(\App\Models\TipoOcorrencia::CATEGORIAS as $k => $v)
                            <option value="{{ $k }}" {{ old('categoria') == $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gravidade Padrão</label>
                    <select name="gravidade" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Selecione</option>
                        <option value="leve" {{ old('gravidade') == 'leve' ? 'selected' : '' }}>Leve</option>
                        <option value="media" {{ old('gravidade') == 'media' ? 'selected' : '' }}>Média</option>
                        <option value="grave" {{ old('gravidade') == 'grave' ? 'selected' : '' }}>Grave</option>
                        <option value="gravissima" {{ old('gravidade') == 'gravissima' ? 'selected' : '' }}>Gravíssima</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                <textarea name="descricao" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('descricao') }}</textarea>
            </div>
            <div class="border border-gray-100 rounded-lg p-3 bg-gray-50 space-y-2">
                <p class="text-xs font-semibold text-gray-500 uppercase">Campos de ponto/atraso ao registrar</p>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="hidden" name="permite_tempo_atraso" value="0">
                    <input type="checkbox" name="permite_tempo_atraso" value="1" {{ old('permite_tempo_atraso') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    Solicitar <strong>tempo de atraso</strong> (minutos)
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="hidden" name="permite_tipo_ponto" value="0">
                    <input type="checkbox" name="permite_tipo_ponto" value="1" {{ old('permite_tipo_ponto') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    Solicitar <strong>tipo de ponto</strong> (entrada/saída/intervalo)
                </label>
            </div>
            <div class="flex items-center gap-3">
                <input type="checkbox" name="ativo" id="ativo" value="1" {{ old('ativo', '1') ? 'checked' : '' }}
                       class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <label for="ativo" class="text-sm text-gray-700">Tipo ativo</label>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3 mt-4">
            <a href="{{ route('tipos-ocorrencias.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">Cancelar</a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Salvar Tipo</button>
        </div>
    </form>
</div>
@endsection
