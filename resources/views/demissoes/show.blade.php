@extends('layouts.app')

@section('title', 'Desligamento')
@section('page-title', 'Detalhes do Desligamento')

@section('content')
<div class="space-y-6 py-4 max-w-2xl">
    <div class="flex items-center gap-3">
        <a href="{{ route('demissoes.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    @php $cor = ['PEDIDO' => 'blue', 'SEM_JUSTA_CAUSA' => 'yellow', 'JUSTA_CAUSA' => 'red', 'FIM_CONTRATO' => 'gray', 'ACORDO' => 'purple'][$demissao->tipo] ?? 'gray'; @endphp
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="mb-4"><x-ui.badge :color="$cor">{{ $demissao->tipoLabel() }}</x-ui.badge></div>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500">Colaborador</dt><dd class="text-gray-900">{{ optional($demissao->colaborador)->nome ?: '—' }}</dd></div>
            <div><dt class="text-gray-500">Empresa</dt><dd class="text-gray-900">{{ optional($demissao->empresa)->nome ?: '—' }}</dd></div>
            <div><dt class="text-gray-500">Cargo</dt><dd class="text-gray-900">{{ optional(optional($demissao->colaborador)->cargo)->nome ?: '—' }}</dd></div>
            <div><dt class="text-gray-500">Data do desligamento</dt><dd class="text-gray-900">{{ optional($demissao->data_desligamento)->format('d/m/Y') }}</dd></div>
            <div><dt class="text-gray-500">Aviso prévio</dt><dd class="text-gray-900">{{ $demissao->avisoLabel() ?: '—' }}</dd></div>
            <div><dt class="text-gray-500">Registrado por</dt><dd class="text-gray-900">{{ optional($demissao->registradoPor)->name ?: '—' }}</dd></div>
            @if($demissao->motivo)
                <div class="sm:col-span-2"><dt class="text-gray-500">Motivo / observações</dt><dd class="text-gray-900 whitespace-pre-line">{{ $demissao->motivo }}</dd></div>
            @endif
        </dl>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-1">Reverter desligamento</h3>
        <p class="text-sm text-gray-500 mb-4">Remove o registro e reativa o colaborador (status ATIVO, data de demissão limpa).</p>
        <form method="POST" action="{{ route('demissoes.destroy', $demissao) }}" x-data @submit.prevent="if(confirm('Reverter desligamento e reativar o colaborador?')) $el.submit()">
            @csrf @method('DELETE')
            <button class="px-5 py-2.5 bg-white border border-red-300 hover:bg-red-50 text-red-700 text-sm font-medium rounded-lg transition">Reverter e reativar colaborador</button>
        </form>
    </div>
</div>
@endsection
