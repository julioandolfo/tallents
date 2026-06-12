@extends('layouts.portal')

@section('title', 'Loja de Pontos')

@section('content')
<div class="space-y-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-6 text-white shadow-sm flex items-center justify-between">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold">Loja de Pontos</h2>
            <p class="mt-1 text-sm text-indigo-100">Troque seus pontos por recompensas</p>
        </div>
        <div class="text-right">
            <p class="text-xs text-indigo-100 uppercase tracking-wide">Seu saldo</p>
            <p class="text-3xl font-bold">{{ $saldo }} <span class="text-base font-medium">pts</span></p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @error('recompensa')
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">{{ $message }}</div>
    @enderror

    {{-- Recompensas --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($recompensas as $rec)
            @php $podeResgatar = $rec->disponivel() && $saldo >= $rec->custo_pontos; @endphp
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
                @if($rec->imagemUrl())
                    <img src="{{ $rec->imagemUrl() }}" class="h-32 w-full object-cover" alt="{{ $rec->nome }}">
                @else
                    <div class="h-32 w-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center">
                        <svg class="h-10 w-10 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    </div>
                @endif
                <div class="p-4 flex flex-col flex-1">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900">{{ $rec->nome }}</h3>
                        <span class="text-sm font-bold text-indigo-600">{{ $rec->custo_pontos }} pts</span>
                    </div>
                    @if($rec->descricao)<p class="text-xs text-gray-500 mt-1 flex-1">{{ $rec->descricao }}</p>@endif
                    <form method="POST" action="{{ route('portal.loja.resgatar', $rec) }}" class="mt-3" x-data @submit.prevent="if(confirm('Resgatar {{ $rec->nome }} por {{ $rec->custo_pontos }} pontos?')) $el.submit()">
                        @csrf
                        <button {{ $podeResgatar ? '' : 'disabled' }}
                                class="w-full px-4 py-2 text-sm font-medium rounded-lg transition {{ $podeResgatar ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}">
                            {{ $rec->disponivel() ? ($saldo >= $rec->custo_pontos ? 'Resgatar' : 'Pontos insuficientes') : 'Indisponível' }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="sm:col-span-2 lg:col-span-3 bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-12 text-center text-sm text-gray-400">Nenhuma recompensa disponível no momento</div>
        @endforelse
    </div>

    {{-- Meus resgates --}}
    @if($meusResgates->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100"><h3 class="text-base font-semibold text-gray-900">Meus resgates</h3></div>
            <div class="divide-y divide-gray-100">
                @foreach($meusResgates as $r)
                    <div class="px-6 py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ optional($r->recompensa)->nome ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $r->created_at->format('d/m/Y') }} · {{ $r->custo_pontos }} pts</p>
                        </div>
                        <x-ui.badge :color="$r->cor()">{{ $r->statusLabel() }}</x-ui.badge>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
