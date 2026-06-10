@extends('layouts.app')

@section('title', 'Promoção')
@section('page-title', 'Detalhes da Promoção')

@section('content')
<div class="space-y-6 py-4">

    <div class="flex items-center gap-3">
        <a href="{{ route('promocoes.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500">Colaborador</dt><dd class="text-gray-900">{{ optional($promocao->colaborador)->nome ?: '—' }}</dd></div>
            <div><dt class="text-gray-500">Data</dt><dd class="text-gray-900">{{ optional($promocao->data_promocao)->format('d/m/Y') }}</dd></div>
            <div><dt class="text-gray-500">Cargo anterior</dt><dd class="text-gray-900">{{ optional($promocao->cargoAnterior)->nome ?: '—' }}</dd></div>
            <div><dt class="text-gray-500">Novo cargo</dt><dd class="text-gray-900">{{ optional($promocao->cargoNovo)->nome ?: '—' }}</dd></div>
            <div><dt class="text-gray-500">Salário anterior</dt><dd class="text-gray-900">R$ {{ number_format((float) $promocao->salario_anterior, 2, ',', '.') }}</dd></div>
            <div><dt class="text-gray-500">Novo salário</dt><dd class="text-gray-900">R$ {{ number_format((float) $promocao->salario_novo, 2, ',', '.') }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-gray-500">Motivo</dt><dd class="text-gray-900 whitespace-pre-line">{{ $promocao->motivo ?: '—' }}</dd></div>
            <div><dt class="text-gray-500">Registrado por</dt><dd class="text-gray-900">{{ optional($promocao->registradoPor)->name ?: '—' }}</dd></div>
        </dl>
    </div>
</div>
@endsection
