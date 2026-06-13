@extends('layouts.app')

@section('title', 'Novo Contrato')
@section('page-title', 'Novo Contrato')

@section('content')
<div class="py-4" x-data="{ conteudo: @js(old('conteudo', '')), preview: false }">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('contratos.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h2 class="text-lg font-semibold text-gray-900">Novo Contrato</h2>
    </div>

    <form method="POST" action="{{ route('contratos.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 form-grid">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Colaborador <span class="text-red-500">*</span></label>
            <select name="colaborador_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Selecione</option>
                @foreach($colaboradores as $c)
                    <option value="{{ $c->id }}" {{ old('colaborador_id', $colaboradorId) == $c->id ? 'selected' : '' }}>{{ $c->nome }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo <span class="text-red-500">*</span></label>
            <select name="tipo" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @foreach(\App\Models\Contrato::TIPOS as $k => $v)<option value="{{ $k }}" {{ old('tipo') == $k ? 'selected' : '' }}>{{ $v }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Título <span class="text-red-500">*</span></label>
            <input type="text" name="titulo" value="{{ old('titulo') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Método de assinatura</label>
            <select name="metodo_assinatura" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @foreach(\App\Models\Contrato::METODOS as $k => $v)<option value="{{ $k }}" {{ old('metodo_assinatura') == $k ? 'selected' : '' }}>{{ $v }}</option>@endforeach
            </select>
            <p class="text-xs text-gray-400 mt-1">Autentique requer o token configurado em Integrações.</p>
        </div>

        {{-- Conteúdo + variáveis + preview (linha inteira) --}}
        <div class="form-full">
            <div class="flex items-center justify-between mb-1">
                <label class="block text-sm font-medium text-gray-700">Conteúdo (texto do contrato)</label>
                <button type="button" @click="preview = !preview" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium" x-text="preview ? 'Editar' : 'Pré-visualizar'"></button>
            </div>

            <textarea x-show="!preview" name="conteudo" x-model="conteudo" rows="12"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono">{{ old('conteudo') }}</textarea>

            <div x-show="preview" x-cloak class="border border-gray-200 rounded-lg bg-gray-50 px-6 py-5 min-h-[18rem]">
                <p class="text-xs text-gray-400 mb-3">Pré-visualização — as variáveis são substituídas pelos dados do colaborador ao salvar.</p>
                <div class="text-sm text-gray-800 whitespace-pre-line leading-relaxed text-justify" x-text="conteudo || 'Sem conteúdo...'"></div>
            </div>

            @php $varsHint = collect(explode(',', \App\Models\Contrato::VARIAVEIS))->map(fn($v) => '{'.'{ '.trim($v).' }'.'}')->implode('  '); @endphp
            <p class="text-xs text-gray-500 mt-2">
                Variáveis disponíveis: <code class="text-indigo-600">{{ $varsHint }}</code>
            </p>
        </div>

        <div class="form-full">
            <label class="block text-sm font-medium text-gray-700 mb-1">Arquivo PDF (opcional — anexa um documento pronto)</label>
            <input type="file" name="arquivo" accept="application/pdf" class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 file:text-sm file:font-medium">
        </div>

        <div class="form-full flex justify-end gap-2 pt-2">
            <a href="{{ route('contratos.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">Cancelar</a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Criar Contrato</button>
        </div>
    </form>
</div>
@endsection
