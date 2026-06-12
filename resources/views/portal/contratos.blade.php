@extends('layouts.portal')

@section('title', 'Meus Contratos')

@section('content')
<div class="space-y-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-6 text-white shadow-sm">
        <h2 class="text-xl sm:text-2xl font-bold">Meus Contratos e Documentos</h2>
        <p class="mt-1 text-sm text-indigo-100">Leia e assine seus documentos digitalmente</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="divide-y divide-gray-100">
            @forelse($contratos as $ct)
                <a href="{{ route('portal.contratos.show', $ct) }}" class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $ct->titulo }}</p>
                        <p class="text-xs text-gray-400">{{ $ct->tipoLabel() }} · {{ $ct->created_at->format('d/m/Y') }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        @if($ct->status === 'PENDENTE')<span class="text-xs font-medium text-amber-600">Aguardando assinatura</span>@endif
                        <x-ui.badge :color="$ct->cor()">{{ $ct->statusLabel() }}</x-ui.badge>
                    </div>
                </a>
            @empty
                <div class="px-6 py-12 text-center text-sm text-gray-400">Você não possui contratos</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
