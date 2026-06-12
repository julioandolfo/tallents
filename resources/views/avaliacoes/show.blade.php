@extends('layouts.app')

@section('title', 'Avaliação')
@section('page-title', 'Avaliação de Desempenho')

@section('content')
<div class="space-y-4 py-4 max-w-3xl">
    <div class="flex items-center justify-between">
        <a href="{{ route('avaliacoes.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <a href="{{ route('avaliacoes.edit', $avaliacao) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Editar</a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ optional($avaliacao->colaborador)->nome ?? '—' }}</h2>
                <p class="text-sm text-gray-500">{{ optional(optional($avaliacao->colaborador)->cargo)->nome ?? '—' }} · Ciclo {{ $avaliacao->ciclo }} · {{ $avaliacao->tipoLabel() }}</p>
            </div>
            <div class="text-right">
                @if(!is_null($avaliacao->nota_geral))
                    <div class="text-3xl font-bold text-indigo-600">{{ number_format($avaliacao->nota_geral, 1, ',', '.') }}</div>
                    <div class="text-xs text-gray-400">de 10</div>
                @endif
            </div>
        </div>
        <div class="mt-3"><x-ui.badge :color="$avaliacao->status === 'CONCLUIDA' ? 'green' : 'yellow'">{{ $avaliacao->statusLabel() }}</x-ui.badge></div>

        <dl class="mt-5 space-y-4 text-sm">
            @if($avaliacao->pontos_fortes)
                <div><dt class="text-gray-500 font-medium">Pontos fortes</dt><dd class="mt-1 text-gray-800 whitespace-pre-line">{{ $avaliacao->pontos_fortes }}</dd></div>
            @endif
            @if($avaliacao->pontos_melhorar)
                <div><dt class="text-gray-500 font-medium">Pontos a melhorar</dt><dd class="mt-1 text-gray-800 whitespace-pre-line">{{ $avaliacao->pontos_melhorar }}</dd></div>
            @endif
            <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                <div><dt class="text-gray-500">Avaliador</dt><dd class="text-gray-800">{{ optional($avaliacao->avaliador)->name ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Data</dt><dd class="text-gray-800">{{ optional($avaliacao->data)->format('d/m/Y') ?? '—' }}</dd></div>
            </div>
        </dl>
    </div>
</div>
@endsection
