@extends('layouts.app')

@section('title', 'Avaliações')
@section('page-title', 'Avaliações de Desempenho')

@section('content')
<div class="space-y-4 py-4">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $avaliacoes->total() }} avaliação(ões)</p>
        <a href="{{ route('avaliacoes.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nova Avaliação
        </a>
    </div>

    <form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex flex-wrap items-end gap-3">
        <div class="w-40">
            <label class="block text-xs font-medium text-gray-600 mb-1">Ciclo</label>
            <select name="ciclo" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Todos</option>
                @foreach($ciclos as $c)<option value="{{ $c }}" {{ request('ciclo') == $c ? 'selected' : '' }}>{{ $c }}</option>@endforeach
            </select>
        </div>
        <div class="w-40">
            <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Todos</option>
                @foreach(\App\Models\Avaliacao::STATUS as $k => $v)<option value="{{ $k }}" {{ request('status') == $k ? 'selected' : '' }}>{{ $v }}</option>@endforeach
            </select>
        </div>
        <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Filtrar</button>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Colaborador</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Ciclo</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nota</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($avaliacoes as $av)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ optional($av->colaborador)->nome ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $av->ciclo }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $av->tipoLabel() }}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ !is_null($av->nota_geral) ? number_format($av->nota_geral, 1, ',', '.') : '—' }}</td>
                        <td class="px-6 py-4"><x-ui.badge :color="$av->status === 'CONCLUIDA' ? 'green' : 'yellow'">{{ $av->statusLabel() }}</x-ui.badge></td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('avaliacoes.show', $av) }}" class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded transition">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('avaliacoes.destroy', $av) }}" x-data @submit.prevent="if(confirm('Excluir avaliação?')) $el.submit()">
                                    @csrf @method('DELETE')
                                    <button class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-sm text-gray-400">Nenhuma avaliação</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($avaliacoes->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">{{ $avaliacoes->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
