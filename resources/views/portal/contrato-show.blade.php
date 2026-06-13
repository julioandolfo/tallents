@extends('layouts.portal')

@section('title', $contrato->titulo)

@section('content')
<div class="space-y-5 max-w-3xl mx-auto">
    <a href="{{ route('portal.contratos.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Voltar
    </a>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-2 mb-2">
            <x-ui.badge :color="$contrato->cor()">{{ $contrato->statusLabel() }}</x-ui.badge>
            <span class="text-xs text-gray-400">{{ $contrato->tipoLabel() }}</span>
        </div>
        <h1 class="text-xl font-bold text-gray-900">{{ $contrato->titulo }}</h1>

        @if($contrato->arquivoUrl())
            <a href="{{ $contrato->arquivoUrl() }}" target="_blank" class="mt-3 inline-flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Abrir documento
            </a>
        @endif

        @if($contrato->conteudo)
            <div class="mt-5 text-sm text-gray-700 whitespace-pre-line leading-relaxed border-t border-gray-100 pt-5 max-h-96 overflow-y-auto text-justify">{{ $contrato->conteudoRenderizado() }}</div>
        @endif
    </div>

    @if($contrato->status === 'ASSINADO')
        <div class="bg-green-50 border border-green-200 text-green-800 px-5 py-4 rounded-xl text-sm">
            ✓ Documento assinado @if($contrato->assinado_em) em {{ $contrato->assinado_em->format('d/m/Y \à\s H:i') }} @endif.
        </div>
    @elseif($contrato->status === 'PENDENTE' && $contrato->metodo_assinatura === 'AUTENTIQUE')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
            <h3 class="text-base font-semibold text-gray-900 mb-2">Assinatura eletrônica</h3>
            <p class="text-sm text-gray-500 mb-4">Este documento usa assinatura eletrônica via Autentique. Você também recebeu o link por e-mail.</p>
            @if($contrato->autentique_signature_url)
                <a href="{{ $contrato->autentique_signature_url }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 px-6 py-2.5 bg-violet-600 hover:bg-violet-700 text-white text-sm font-medium rounded-lg transition">Assinar via Autentique</a>
            @else
                <p class="text-sm text-gray-400">Aguarde — o link de assinatura está sendo gerado.</p>
            @endif
        </div>
    @elseif($contrato->status === 'PENDENTE')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-3">Assinatura digital</h3>
            <form method="POST" action="{{ route('portal.contratos.assinar', $contrato) }}" x-data="{ ok: false }">
                @csrf
                <label class="flex items-start gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="aceite" value="1" x-model="ok" class="mt-0.5 rounded border-gray-300 text-indigo-600">
                    Li e concordo com os termos deste documento. Reconheço que esta ação tem validade de assinatura, sendo registradas a data, a hora e o meu endereço IP.
                </label>
                @error('aceite')<p class="mt-1 text-xs text-red-600">É necessário aceitar os termos.</p>@enderror
                <div class="mt-4 flex justify-end">
                    <button type="submit" :disabled="!ok" :class="ok ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                            class="px-6 py-2.5 text-sm font-medium rounded-lg transition">Assinar documento</button>
                </div>
            </form>
        </div>
    @else
        <div class="bg-gray-50 border border-gray-200 text-gray-600 px-5 py-4 rounded-xl text-sm">Este contrato foi cancelado.</div>
    @endif
</div>
@endsection
