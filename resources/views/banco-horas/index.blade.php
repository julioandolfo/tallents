@extends('layouts.app')

@section('title', 'Banco de Horas')
@section('page-title', 'Banco de Horas')

@section('content')
<div class="space-y-4 py-4">

    <form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 grid grid-cols-1 sm:grid-cols-4 gap-3">
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-600 mb-1">Buscar</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome do colaborador"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Empresa</label>
            <select name="empresa_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Todas</option>
                @foreach($empresas as $e)
                    <option value="{{ $e->id }}" {{ request('empresa_id') == $e->id ? 'selected' : '' }}>{{ $e->nome }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Filtrar</button>
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Colaborador</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Empresa</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Saldo</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Extrato</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($colaboradores as $c)
                    @php $saldo = round(($c->creditos ?? 0) - ($c->debitos ?? 0), 2); @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $c->nome }}</td>
                        <td class="px-6 py-4 hidden md:table-cell text-sm text-gray-600">{{ optional($c->empresa)->nome ?? '—' }}</td>
                        <td class="px-6 py-4 text-right text-sm font-semibold {{ $saldo < 0 ? 'text-red-600' : ($saldo > 0 ? 'text-emerald-600' : 'text-gray-500') }}">
                            {{ number_format($saldo, 2, ',', '.') }}h
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('banco-horas.extrato', $c) }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Ver / Lançar</a>
                        </td>
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
