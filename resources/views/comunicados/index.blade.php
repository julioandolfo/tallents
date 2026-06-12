@extends('layouts.app')

@section('title', 'Comunicados')
@section('page-title', 'Comunicados')

@section('content')
<div class="space-y-4 py-4">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $comunicados->total() }} comunicado(s)</p>
        <a href="{{ route('comunicados.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Novo Comunicado
        </a>
    </div>

    <form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex flex-wrap items-end gap-3">
        <div class="w-48">
            <label class="block text-xs font-medium text-gray-600 mb-1">Categoria</label>
            <select name="categoria" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Todas</option>
                @foreach(\App\Models\Comunicado::CATEGORIAS as $k => $v)
                    <option value="{{ $k }}" {{ request('categoria') == $k ? 'selected' : '' }}>{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Filtrar</button>
    </form>

    <div class="space-y-3">
        @forelse($comunicados as $com)
            <div class="bg-white rounded-xl shadow-sm border {{ $com->destaque ? 'border-indigo-300 ring-1 ring-indigo-100' : 'border-gray-200' }} p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-2 flex-wrap">
                        <x-ui.badge :color="$com->cor()">{{ $com->categoriaLabel() }}</x-ui.badge>
                        @if($com->destaque)<x-ui.badge color="indigo">Destaque</x-ui.badge>@endif
                        @if(!$com->publicado)<x-ui.badge color="gray">Rascunho</x-ui.badge>@endif
                    </div>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('comunicados.show', $com) }}" class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded transition" title="Ver">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        <a href="{{ route('comunicados.edit', $com) }}" class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded transition" title="Editar">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('comunicados.destroy', $com) }}" x-data @submit.prevent="if(confirm('Excluir comunicado?')) $el.submit()">
                            @csrf @method('DELETE')
                            <button class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition" title="Excluir">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
                <a href="{{ route('comunicados.show', $com) }}" class="block mt-2">
                    <h3 class="text-base font-semibold text-gray-900 hover:text-indigo-600">{{ $com->titulo }}</h3>
                </a>
                <p class="mt-1 text-sm text-gray-600 line-clamp-2">{{ \Illuminate\Support\Str::limit(strip_tags($com->conteudo), 180) }}</p>
                <p class="mt-2 text-xs text-gray-400">
                    {{ optional($com->autor)->name ?? 'Sistema' }} ·
                    {{ optional($com->publicado_em ?? $com->created_at)->format('d/m/Y H:i') }}
                    {{ $com->empresa ? '· ' . $com->empresa->nome : '· Todas as empresas' }}
                </p>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-12 text-center text-sm text-gray-400">Nenhum comunicado</div>
        @endforelse
    </div>

    @if($comunicados->hasPages())
        <div>{{ $comunicados->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
