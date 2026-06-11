@extends('layouts.app')

@section('title', 'Fechamentos')
@section('page-title', 'Fechamento Mensal')

@section('content')
<div class="space-y-4 py-4">

    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $fechamentos->total() ?? 0 }} fechamentos</p>
        <a href="{{ route('fechamentos.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Novo Fechamento
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Empresa</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Competência</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Total Colaboradores</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Total Ocorrências</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($fechamentos ?? [] as $fechamento)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $fechamento->empresa->nome ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                            {{ isset($fechamento->mes) && isset($fechamento->ano) ? str_pad($fechamento->mes, 2, '0', STR_PAD_LEFT) . '/' . $fechamento->ano : '—' }}
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell text-sm text-gray-600">{{ $fechamento->total_colaboradores ?? '—' }}</td>
                        <td class="px-6 py-4 hidden lg:table-cell text-sm text-gray-600">{{ $fechamento->total_ocorrencias ?? '—' }}</td>
                        <td class="px-6 py-4">
                            @php
                                $st = [
                                    'aberto'    => 'bg-yellow-100 text-yellow-800',
                                    'fechado'   => 'bg-green-100 text-green-800',
                                    'revisao'   => 'bg-blue-100 text-blue-800',
                                ];
                                $sc = $st[$fechamento->status ?? 'aberto'] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sc }}">
                                {{ ucfirst($fechamento->status ?? 'aberto') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('fechamentos.show', $fechamento) }}"
                                   class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded transition">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('fechamentos.destroy', $fechamento) }}"
                                      x-data @submit.prevent="if(confirm('Excluir este fechamento?')) $el.submit()">
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
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-400">Nenhum fechamento registrado</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if(isset($fechamentos) && $fechamentos->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">{{ $fechamentos->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
