@extends('layouts.app')

@section('title', 'Pontos — ' . $colaborador->nome)
@section('page-title', 'Extrato de Pontos')

@section('content')
<div class="space-y-6 py-4 max-w-3xl">
    <div class="flex items-center gap-3">
        <a href="{{ route('pontos.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-lg font-semibold text-gray-900">{{ $colaborador->nome }}</h2>
            <p class="text-sm text-gray-500">{{ optional($colaborador->cargo)->nome ?? '—' }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col items-center justify-center">
            <p class="text-sm text-gray-500">Saldo de pontos</p>
            <p class="mt-1 text-4xl font-bold text-indigo-600">{{ $saldo }}</p>
        </div>
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Lançar pontos</h3>
            <form method="POST" action="{{ route('pontos.lancar', $colaborador) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pontos (use negativo para retirar)</label>
                    <input type="number" name="pontos" required placeholder="Ex: 50" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Origem</label>
                    <select name="origem" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach(['MANUAL','ELOGIO','AVALIACAO','META','AJUSTE'] as $o)
                            <option value="{{ $o }}">{{ \App\Models\PontoMovimentacao::ORIGENS[$o] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                    <input type="text" name="descricao" placeholder="Motivo do lançamento" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="sm:col-span-2 flex justify-end"><button class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Lançar</button></div>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200"><h3 class="text-base font-semibold text-gray-900">Movimentações</h3></div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Origem</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Descrição</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Pontos</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($movimentacoes as $mov)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $mov->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $mov->origemLabel() }}</td>
                        <td class="px-6 py-3 hidden md:table-cell text-sm text-gray-600">{{ $mov->descricao ?: '—' }}</td>
                        <td class="px-6 py-3 text-right text-sm font-semibold {{ $mov->pontos < 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ $mov->pontos > 0 ? '+' : '' }}{{ $mov->pontos }}</td>
                        <td class="px-6 py-3 text-right">
                            <form method="POST" action="{{ route('pontos.movimentacoes.destroy', $mov) }}" x-data @submit.prevent="if(confirm('Remover lançamento?')) $el.submit()">
                                @csrf @method('DELETE')
                                <button class="p-1.5 text-gray-400 hover:text-red-600 rounded"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-10 text-center text-sm text-gray-400">Nenhuma movimentação</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($movimentacoes->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">{{ $movimentacoes->links() }}</div>
        @endif
    </div>
</div>
@endsection
