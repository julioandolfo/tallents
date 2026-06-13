@extends('layouts.app')

@section('title', 'Ocorrências')
@section('page-title', 'Ocorrências')

@section('content')
<div class="space-y-4 py-4">

    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $ocorrencias->total() ?? 0 }} ocorrências encontradas</p>
        <div class="flex items-center gap-2">
            <a href="{{ route('exportar.ocorrencias.csv', request()->query()) }}"
               class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">CSV</a>
            <a href="{{ route('exportar.ocorrencias.pdf', request()->query()) }}"
               class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">PDF</a>
            <a href="{{ route('ocorrencias.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nova Ocorrência
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('ocorrencias.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Buscar colaborador</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome..."
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="min-w-[160px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Tipo</label>
                <select name="tipo_ocorrencia_id" class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Todos</option>
                    @foreach($tiposOcorrencias ?? [] as $tipo)
                        <option value="{{ $tipo->id }}" {{ request('tipo_ocorrencia_id') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[130px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Data início</label>
                <input type="date" name="data_inicio" value="{{ request('data_inicio') }}"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="min-w-[130px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Data fim</label>
                <input type="date" name="data_fim" value="{{ request('data_fim') }}"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Filtrar</button>
                <a href="{{ route('ocorrencias.index') }}" class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">Limpar</a>
            </div>
        </form>
    </div>

    <!-- Tabela -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Colaborador</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Empresa</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Observação</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($ocorrencias ?? [] as $ocorrencia)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $ocorrencia->colaborador->nome ?? '—' }}</td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                {{ $ocorrencia->tipoOcorrencia->nome ?? '—' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 hidden lg:table-cell text-sm text-gray-600">{{ $ocorrencia->colaborador->empresa->nome ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ \Carbon\Carbon::parse($ocorrencia->data)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 hidden lg:table-cell text-sm text-gray-600 max-w-xs truncate">{{ $ocorrencia->observacao ?? '—' }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('ocorrencias.edit', $ocorrencia) }}"
                                   class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded transition">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('ocorrencias.destroy', $ocorrencia) }}"
                                      x-data @submit.prevent="if(confirm('Excluir esta ocorrência?')) $el.submit()">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-400">Nenhuma ocorrência registrada</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if(isset($ocorrencias) && $ocorrencias->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">{{ $ocorrencias->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
