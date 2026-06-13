@extends('layouts.app')
@section('title', 'Editar Template')
@section('page-title', 'Editar Template')
@section('content')
<div class="py-4 max-w-3xl space-y-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('configuracoes.templates.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h2 class="text-lg font-semibold text-gray-900">{{ $template->nome }}</h2>
    </div>

    @if($errors->any())<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">{{ $errors->first() }}</div>@endif

    <form method="POST" action="{{ route('configuracoes.templates.update', $template) }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Assunto</label>
            <input type="text" name="assunto" value="{{ old('assunto', $template->assunto) }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Corpo (HTML)</label>
            <textarea name="corpo" rows="12" required
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('corpo', $template->corpo) }}</textarea>
            @if($template->variaveis)
                <p class="text-xs text-gray-500 mt-1">Variáveis disponíveis: <code class="text-indigo-600">{{ collect(explode(',', $template->variaveis))->map(fn($v) => '{{ '.trim($v).' }}')->implode(', ') }}</code></p>
            @endif
        </div>
        <label class="flex items-center gap-2 text-sm text-gray-700">
            <input type="hidden" name="ativo" value="0">
            <input type="checkbox" name="ativo" value="1" {{ old('ativo', $template->ativo) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
            Template ativo
        </label>
        <div class="flex justify-end gap-3">
            <a href="{{ route('configuracoes.templates.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">Cancelar</a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Salvar</button>
        </div>
    </form>
</div>
@endsection
