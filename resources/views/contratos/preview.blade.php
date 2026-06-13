@extends('layouts.app')
@section('title', 'Pré-visualização')
@section('page-title', 'Pré-visualização do Contrato')
@section('content')
<div class="py-4 max-w-4xl mx-auto space-y-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('contratos.show', $contrato) }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h2 class="text-lg font-semibold text-gray-900">Pré-visualização</h2>
        <div class="flex-1"></div>
        <a href="{{ route('contratos.pdf', $contrato) }}" class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">Baixar PDF</a>
    </div>

    {{-- Folha estilo documento --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-10 py-12">
        <div class="border-b-2 border-indigo-600 pb-3 mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ $contrato->titulo }}</h1>
            <p class="text-xs text-gray-500 mt-1">{{ $contrato->tipoLabel() }} · {{ optional($contrato->colaborador)->nome }} · {{ now()->format('d/m/Y') }}</p>
        </div>
        <div class="prose max-w-none text-sm text-gray-800 whitespace-pre-line leading-relaxed text-justify">{{ $contrato->conteudoRenderizado() }}</div>
    </div>
</div>
@endsection
