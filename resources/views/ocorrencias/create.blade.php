@extends('layouts.app')

@section('title', 'Nova Ocorrência')
@section('page-title', 'Nova Ocorrência')

@section('content')
<div class="py-4 max-w-2xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('ocorrencias.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h2 class="text-lg font-semibold text-gray-900">Registrar Nova Ocorrência</h2>
    </div>

    <form method="POST" action="{{ route('ocorrencias.store') }}">
        @csrf
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Colaborador <span class="text-red-500">*</span></label>
                <select name="colaborador_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Selecione o colaborador</option>
                    @foreach($colaboradores ?? [] as $colaborador)
                        <option value="{{ $colaborador->id }}" {{ (old('colaborador_id', request('colaborador_id'))) == $colaborador->id ? 'selected' : '' }}>
                            {{ $colaborador->nome }} — {{ $colaborador->empresa->nome ?? '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Ocorrência <span class="text-red-500">*</span></label>
                <select name="tipo_ocorrencia_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Selecione o tipo</option>
                    @foreach($tiposOcorrencias ?? [] as $tipo)
                        <option value="{{ $tipo->id }}" {{ old('tipo_ocorrencia_id') == $tipo->id ? 'selected' : '' }}>
                            {{ $tipo->nome }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data da Ocorrência <span class="text-red-500">*</span></label>
                <input type="date" name="data" value="{{ old('data', date('Y-m-d')) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gravidade</label>
                <select name="gravidade" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Selecione</option>
                    <option value="leve" {{ old('gravidade') == 'leve' ? 'selected' : '' }}>Leve</option>
                    <option value="media" {{ old('gravidade') == 'media' ? 'selected' : '' }}>Média</option>
                    <option value="grave" {{ old('gravidade') == 'grave' ? 'selected' : '' }}>Grave</option>
                    <option value="gravissima" {{ old('gravidade') == 'gravissima' ? 'selected' : '' }}>Gravíssima</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Observação</label>
                <textarea name="observacao" rows="4" placeholder="Descreva a ocorrência em detalhes..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('observacao') }}</textarea>
            </div>
            <div class="flex items-center gap-3">
                <input type="checkbox" name="notificar_colaborador" id="notificar" value="1" {{ old('notificar_colaborador') ? 'checked' : '' }}
                       class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <label for="notificar" class="text-sm text-gray-700">Notificar o colaborador por e-mail</label>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3 mt-4">
            <a href="{{ route('ocorrencias.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">Cancelar</a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Registrar Ocorrência</button>
        </div>
    </form>
</div>
@endsection
