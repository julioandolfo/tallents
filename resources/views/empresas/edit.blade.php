@extends('layouts.app')

@section('title', 'Editar Empresa')
@section('page-title', 'Editar Empresa')

@section('content')
<div class="py-4 max-w-3xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('empresas.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h2 class="text-lg font-semibold text-gray-900">Editar Empresa: {{ $empresa->nome }}</h2>
    </div>

    <form method="POST" action="{{ route('empresas.update', $empresa) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
            <div>
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Dados da Empresa</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome Fantasia <span class="text-red-500">*</span></label>
                        <input type="text" name="nome" value="{{ old('nome', $empresa->nome) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Razão Social</label>
                        <input type="text" name="razao_social" value="{{ old('razao_social', $empresa->razao_social) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CNPJ</label>
                        <div class="flex gap-2">
                            <input type="text" name="cnpj" value="{{ old('cnpj', $empresa->cnpj) }}"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <button type="button" data-cnpj-buscar class="px-3 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition whitespace-nowrap">Buscar</button>
                        </div>
                        <p data-cnpj-msg class="text-xs text-gray-400 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                        <input type="text" name="telefone" value="{{ old('telefone', $empresa->telefone) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                        <input type="email" name="email" value="{{ old('email', $empresa->email) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
                        <input type="text" name="bairro" value="{{ old('bairro', $empresa->bairro) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                        <input type="text" name="cidade" value="{{ old('cidade', $empresa->cidade) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select name="estado" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Selecione</option>
                            @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                                <option value="{{ $uf }}" {{ old('estado', $empresa->estado) == $uf ? 'selected' : '' }}>{{ $uf }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-4">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Configurações</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Percentual de Hora Extra (%)</label>
                        <input type="number" step="0.01" min="0" name="percentual_hora_extra" value="{{ old('percentual_hora_extra', $empresa->percentual_hora_extra ?? 50) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <p class="text-xs text-gray-400 mt-1">Adicional aplicado no cálculo de horas extras.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                        @if($empresa->logo)<img src="{{ Storage::url($empresa->logo) }}" class="h-10 mb-2">@endif
                        <input type="file" name="logo" accept="image/*" class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 file:text-sm file:font-medium">
                    </div>
                    <label class="flex items-center gap-2 text-sm text-gray-700 sm:col-span-2">
                        <input type="hidden" name="ativa" value="0">
                        <input type="checkbox" name="ativa" value="1" {{ old('ativa', $empresa->ativa) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                        Empresa ativa
                    </label>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 mt-4">
            <a href="{{ route('empresas.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">Cancelar</a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Salvar Alterações</button>
        </div>
    </form>
</div>
@endsection
@include("empresas._cnpj-script")
