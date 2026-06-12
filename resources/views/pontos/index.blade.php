@extends('layouts.app')

@section('title', 'Pontos')
@section('page-title', 'Pontos — Ranking')

@section('content')
<div class="space-y-4 py-4">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-end gap-3">
        <div class="flex-1 max-w-sm">
            <label class="block text-xs font-medium text-gray-600 mb-1">Buscar colaborador</label>
            <input type="text" name="search" value="{{ request('search') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Filtrar</button>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-16">#</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Colaborador</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Saldo</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Lançar / Extrato</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($colaboradores as $i => $c)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm font-bold text-gray-400">{{ $colaboradores->firstItem() + $i }}º</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $c->nome }}</td>
                        <td class="px-6 py-4 text-right text-sm font-bold text-indigo-600">{{ (int) ($c->saldo_pontos ?? 0) }} pts</td>
                        <td class="px-6 py-4 text-right"><a href="{{ route('pontos.extrato', $c) }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Gerenciar</a></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-12 text-center text-sm text-gray-400">Nenhum colaborador</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($colaboradores->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">{{ $colaboradores->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
