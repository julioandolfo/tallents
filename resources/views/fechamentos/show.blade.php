@extends('layouts.app')

@section('title', 'Fechamento')
@section('page-title', 'Detalhes do Fechamento')

@section('content')
@php $aberto = $fechamento->status === 'ABERTO'; @endphp
<div class="space-y-6 py-4">

    <div class="flex items-center gap-3">
        <a href="{{ route('fechamentos.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div class="flex-1">
            <h2 class="text-lg font-semibold text-gray-900">
                {{ $fechamento->empresa->nome ?? '—' }} — {{ str_pad($fechamento->mes ?? '', 2, '0', STR_PAD_LEFT) }}/{{ $fechamento->ano ?? '' }}
            </h2>
        </div>
        <x-ui.badge :color="$aberto ? 'yellow' : 'green'">{{ $aberto ? 'Aberto' : 'Fechado' }}</x-ui.badge>
        @if($fechamento->itens->isNotEmpty())
            <a href="{{ route('exportar.fechamentos.pdf', $fechamento) }}" class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">PDF</a>
        @endif
        @unless($aberto)
            <form method="POST" action="{{ route('fechamentos.reabrir', $fechamento) }}" x-data @submit.prevent="if(confirm('Reabrir este fechamento para edição?')) $el.submit()">
                @csrf
                <button class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">Reabrir</button>
            </form>
        @endunless
    </div>

    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>@endif

    {{-- Resumo --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
            <p class="text-sm text-gray-500">Colaboradores</p>
            <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $fechamento->itens->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
            <p class="text-sm text-gray-500">Total Geral</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">R$ {{ number_format($fechamento->total_geral ?? 0, 2, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
            <p class="text-sm text-gray-500">Processado em</p>
            <p class="text-sm font-medium text-gray-700 mt-2">{{ optional($fechamento->fechado_em)->format('d/m/Y H:i') ?? '—' }}</p>
        </div>
    </div>

    {{-- Processamento (quando aberto) --}}
    @if($aberto)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6" x-data="{ todos: true }">
        <h3 class="text-base font-semibold text-gray-900 mb-1">Processar competência</h3>
        <p class="text-sm text-gray-500 mb-4">Selecione os colaboradores e processe. Fórmula: salário + horas extras + bônus − descontos + adicionais. Ajustes manuais são preservados ao reprocessar.</p>
        <form method="POST" action="{{ route('fechamentos.fechar', $fechamento) }}" x-data @submit.prevent="if(confirm('Processar e fechar a competência?')) $el.submit()">
            @csrf
            <label class="flex items-center gap-2 text-sm text-gray-700 mb-3">
                <input type="checkbox" x-model="todos" @change="document.querySelectorAll('.chk-colab').forEach(c => c.checked = todos)" checked class="rounded border-gray-300 text-indigo-600">
                Selecionar todos
            </label>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 max-h-64 overflow-y-auto mb-4">
                @forelse($ativos as $a)
                    <label class="flex items-center gap-2 text-sm text-gray-700 px-3 py-2 border border-gray-100 rounded-lg">
                        <input type="checkbox" name="colaborador_ids[]" value="{{ $a->id }}" checked class="chk-colab rounded border-gray-300 text-indigo-600">
                        {{ $a->nome }}
                    </label>
                @empty
                    <p class="text-sm text-gray-400">Nenhum colaborador ativo nesta empresa.</p>
                @endforelse
            </div>
            <div class="flex justify-end">
                <button class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Processar e Fechar</button>
            </div>
        </form>
    </div>
    @endif

    {{-- Itens --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200"><h3 class="text-base font-semibold text-gray-900">Demonstrativo por colaborador</h3></div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Colaborador</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Salário</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">H. Extras</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Bônus</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Descontos</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Adicionais</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total</th>
                        @if($aberto)<th class="px-4 py-3"></th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($fechamento->itens as $item)
                        <tr class="hover:bg-gray-50">
                            @if($aberto)
                                <form method="POST" action="{{ route('fechamentos.itens.update', $item) }}" id="item-{{ $item->id }}">@csrf @method('PUT')</form>
                            @endif
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ optional($item->colaborador)->nome ?? '—' }}</td>
                            <td class="px-4 py-3 text-right text-sm text-gray-700">R$ {{ number_format($item->salario_base, 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-sm text-gray-700">R$ {{ number_format($item->total_horas_extras, 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-sm text-gray-700">R$ {{ number_format($item->total_bonus, 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-sm text-red-600">
                                @if($aberto)
                                    <input form="item-{{ $item->id }}" type="number" step="0.01" min="0" name="descontos" value="{{ $item->descontos }}" class="w-24 px-2 py-1 border border-gray-200 rounded text-right text-sm">
                                @else
                                    R$ {{ number_format($item->descontos, 2, ',', '.') }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-sm text-emerald-600">
                                @if($aberto)
                                    <input form="item-{{ $item->id }}" type="number" step="0.01" min="0" name="adicionais" value="{{ $item->adicionais }}" class="w-24 px-2 py-1 border border-gray-200 rounded text-right text-sm">
                                @else
                                    R$ {{ number_format($item->adicionais, 2, ',', '.') }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900">R$ {{ number_format($item->total, 2, ',', '.') }}</td>
                            @if($aberto)
                                <td class="px-4 py-3 text-right"><button form="item-{{ $item->id }}" type="submit" class="text-xs px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">Salvar</button></td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="{{ $aberto ? 8 : 7 }}" class="px-6 py-8 text-center text-sm text-gray-400">Nenhum item — processe a competência acima.</td></tr>
                    @endforelse
                </tbody>
                @if($fechamento->itens->isNotEmpty())
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="6" class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Total Geral</td>
                            <td class="px-4 py-3 text-right text-base font-bold text-emerald-600">R$ {{ number_format($fechamento->itens->sum('total'), 2, ',', '.') }}</td>
                            @if($aberto)<td></td>@endif
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
