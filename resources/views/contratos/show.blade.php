@extends('layouts.app')

@section('title', $contrato->titulo)
@section('page-title', 'Contrato')

@section('content')
<div class="space-y-4 py-4 max-w-3xl">
    <div class="flex items-center justify-between">
        <a href="{{ route('contratos.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        @if($contrato->status !== 'CANCELADO')
            <form method="POST" action="{{ route('contratos.status', $contrato) }}" x-data @submit.prevent="if(confirm('Cancelar contrato?')) $el.submit()">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="CANCELADO">
                <button class="px-4 py-2 bg-white border border-red-300 hover:bg-red-50 text-red-700 text-sm font-medium rounded-lg transition">Cancelar contrato</button>
            </form>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-2 mb-2">
            <x-ui.badge :color="$contrato->cor()">{{ $contrato->statusLabel() }}</x-ui.badge>
            <span class="text-xs text-gray-400">{{ $contrato->tipoLabel() }}</span>
        </div>
        <h1 class="text-xl font-bold text-gray-900">{{ $contrato->titulo }}</h1>
        <p class="mt-1 text-sm text-gray-500">{{ optional($contrato->colaborador)->nome ?? '—' }} · Criado por {{ optional($contrato->criadoPor)->name ?? 'Sistema' }}</p>

        @if($contrato->status === 'ASSINADO')
            <div class="mt-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
                ✓ Assinado em {{ $contrato->assinado_em->format('d/m/Y H:i') }} · IP {{ $contrato->assinatura_ip }}
            </div>
        @endif

        @if($contrato->arquivoUrl())
            <a href="{{ $contrato->arquivoUrl() }}" target="_blank" class="mt-3 inline-flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Abrir arquivo
            </a>
        @endif

        @if($contrato->conteudo)
            <div class="mt-5 text-sm text-gray-700 whitespace-pre-line leading-relaxed border-t border-gray-100 pt-5">{{ $contrato->conteudo }}</div>
        @endif
    </div>
</div>
@endsection
