@extends('layouts.app')

@section('title', 'PDI')
@section('page-title', 'Plano de Desenvolvimento')

@section('content')
<div class="space-y-5 py-4 max-w-3xl">
    <div class="flex items-center gap-3">
        <a href="{{ route('pdis.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    @php $pct = $pdi->progresso(); @endphp
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ $pdi->titulo }}</h2>
                <p class="text-sm text-gray-500">{{ optional($pdi->colaborador)->nome ?? '—' }} · {{ optional(optional($pdi->colaborador)->cargo)->nome ?? '—' }}</p>
            </div>
            <x-ui.badge :color="$pdi->statusCor()">{{ $pdi->statusLabel() }}</x-ui.badge>
        </div>
        @if($pdi->descricao)<p class="mt-3 text-sm text-gray-700 whitespace-pre-line">{{ $pdi->descricao }}</p>@endif
        @if($pdi->prazo)<p class="mt-2 text-xs text-gray-400">Prazo: {{ $pdi->prazo->format('d/m/Y') }}</p>@endif

        <div class="mt-4">
            <div class="flex items-center justify-between text-sm mb-1"><span class="text-gray-500">Progresso</span><span class="font-semibold text-gray-900">{{ $pct }}%</span></div>
            <div class="h-2.5 bg-gray-100 rounded-full overflow-hidden"><div class="h-full bg-emerald-500 rounded-full transition-all" style="width: {{ $pct }}%"></div></div>
        </div>

        <form method="POST" action="{{ route('pdis.update', $pdi) }}" class="mt-4 flex items-end gap-3">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @foreach(\App\Models\Pdi::STATUS as $k => $v)<option value="{{ $k }}" {{ $pdi->status == $k ? 'selected' : '' }}>{{ $v }}</option>@endforeach
                </select>
            </div>
            <button class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition">Atualizar</button>
        </form>
    </div>

    {{-- Ações --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100"><h3 class="text-base font-semibold text-gray-900">Ações de desenvolvimento</h3></div>
        <ul class="divide-y divide-gray-100">
            @forelse($pdi->acoes as $acao)
                <li class="flex items-center gap-3 px-6 py-3 hover:bg-gray-50 transition">
                    <form method="POST" action="{{ route('pdis.acoes.toggle', $acao) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="flex-shrink-0 h-5 w-5 rounded border flex items-center justify-center transition {{ $acao->concluida ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-gray-300 text-transparent hover:border-emerald-400' }}">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </button>
                    </form>
                    <div class="flex-1">
                        <p class="text-sm {{ $acao->concluida ? 'text-gray-400 line-through' : 'text-gray-900' }}">{{ $acao->descricao }}</p>
                        @if($acao->prazo)<p class="text-xs text-gray-400">Prazo: {{ $acao->prazo->format('d/m/Y') }}</p>@endif
                    </div>
                    <form method="POST" action="{{ route('pdis.acoes.destroy', $acao) }}" x-data @submit.prevent="if(confirm('Remover ação?')) $el.submit()">
                        @csrf @method('DELETE')
                        <button class="p-1.5 text-gray-300 hover:text-red-600 rounded transition"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </form>
                </li>
            @empty
                <li class="px-6 py-8 text-center text-sm text-gray-400">Nenhuma ação cadastrada</li>
            @endforelse
        </ul>
        <form method="POST" action="{{ route('pdis.acoes.store', $pdi) }}" class="flex items-center gap-2 px-6 py-4 border-t border-gray-200 bg-gray-50">
            @csrf
            <input type="text" name="descricao" required placeholder="Nova ação de desenvolvimento…" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <input type="date" name="prazo" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Adicionar</button>
        </form>
    </div>

    <div class="flex justify-end">
        <form method="POST" action="{{ route('pdis.destroy', $pdi) }}" x-data @submit.prevent="if(confirm('Excluir PDI?')) $el.submit()">
            @csrf @method('DELETE')
            <button class="px-4 py-2 text-sm text-red-600 hover:text-red-700 font-medium">Excluir PDI</button>
        </form>
    </div>
</div>
@endsection
