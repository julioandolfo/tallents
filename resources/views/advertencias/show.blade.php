@extends('layouts.app')

@section('title', 'Advertência')
@section('page-title', 'Detalhes da Advertência')

@section('content')
<div class="space-y-6 py-4 max-w-2xl">
    <div class="flex items-center gap-3">
        <a href="{{ route('advertencias.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    @php $cor = ['VERBAL' => 'yellow', 'ESCRITA' => 'red', 'SUSPENSAO' => 'purple'][$advertencia->tipo] ?? 'gray'; @endphp
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="mb-4"><x-ui.badge :color="$cor">{{ $advertencia->tipoLabel() }}</x-ui.badge></div>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500">Colaborador</dt><dd class="text-gray-900">{{ optional($advertencia->colaborador)->nome ?: '—' }}</dd></div>
            <div><dt class="text-gray-500">Data</dt><dd class="text-gray-900">{{ optional($advertencia->data)->format('d/m/Y') }}</dd></div>
            @if($advertencia->tipo === 'SUSPENSAO')
                <div><dt class="text-gray-500">Dias de suspensão</dt><dd class="text-gray-900">{{ $advertencia->dias_suspensao ?? '—' }}</dd></div>
            @endif
            <div><dt class="text-gray-500">Aplicada por</dt><dd class="text-gray-900">{{ optional($advertencia->aplicadaPor)->name ?: '—' }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-gray-500">Motivo</dt><dd class="text-gray-900 whitespace-pre-line">{{ $advertencia->motivo }}</dd></div>
            @if($advertencia->ocorrencia_id)
                <div class="sm:col-span-2"><dt class="text-gray-500">Ocorrência de origem</dt>
                    <dd><a href="{{ route('ocorrencias.show', $advertencia->ocorrencia_id) }}" class="text-indigo-600 hover:text-indigo-700 font-medium">Ver ocorrência #{{ $advertencia->ocorrencia_id }}</a></dd>
                </div>
            @endif
        </dl>
    </div>
</div>
@endsection
