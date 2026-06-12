@extends('layouts.app')

@section('title', 'Vagas')
@section('page-title', 'Recrutamento — Vagas')

@section('content')
<div class="space-y-4 py-4">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $vagas->total() }} vaga(s)</p>
        <a href="{{ route('vagas.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nova Vaga
        </a>
    </div>

    <form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex flex-wrap items-end gap-3">
        <div class="w-48">
            <label class="block text-xs font-medium text-gray-600 mb-1">Empresa</label>
            <select name="empresa_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Todas</option>
                @foreach($empresas as $e)<option value="{{ $e->id }}" {{ request('empresa_id') == $e->id ? 'selected' : '' }}>{{ $e->nome }}</option>@endforeach
            </select>
        </div>
        <div class="w-40">
            <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Todos</option>
                @foreach(\App\Models\Vaga::STATUS as $k => $v)<option value="{{ $k }}" {{ request('status') == $k ? 'selected' : '' }}>{{ $v }}</option>@endforeach
            </select>
        </div>
        <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Filtrar</button>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($vagas as $vaga)
            <a href="{{ route('vagas.show', $vaga) }}" class="block bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:border-indigo-300 hover:shadow transition">
                <div class="flex items-center justify-between mb-2">
                    <x-ui.badge :color="$vaga->statusCor()">{{ $vaga->statusLabel() }}</x-ui.badge>
                    <span class="text-xs text-gray-400">{{ $vaga->modalidadeLabel() }}</span>
                </div>
                <h3 class="text-base font-semibold text-gray-900">{{ $vaga->titulo }}</h3>
                <p class="text-sm text-gray-500">{{ optional($vaga->empresa)->nome ?? '—' }}</p>
                <div class="mt-3 flex items-center justify-between text-sm">
                    <span class="text-gray-500">{{ $vaga->candidatos_count }} candidato(s)</span>
                    <span class="text-gray-500">{{ $vaga->quantidade }} vaga(s)</span>
                </div>
            </a>
        @empty
            <div class="md:col-span-2 xl:col-span-3 bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-12 text-center text-sm text-gray-400">Nenhuma vaga cadastrada</div>
        @endforelse
    </div>

    @if($vagas->hasPages())
        <div>{{ $vagas->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
