@extends('layouts.app')

@section('title', 'Promoções')
@section('page-title', 'Promoções')

@section('content')
<div class="space-y-4 py-4">

    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $promocoes->total() ?? 0 }} promoções registradas</p>
        <a href="{{ route('promocoes.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Registrar Promoção
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Colaborador</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Cargo Anterior</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Novo Cargo</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Novo Salário</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($promocoes ?? [] as $promocao)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $promocao->colaborador->nome ?? '—' }}</td>
                        <td class="px-6 py-4 hidden md:table-cell text-sm text-gray-600">{{ $promocao->cargoAnterior->nome ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 text-sm font-medium text-green-700">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                </svg>
                                {{ $promocao->novoCargo->nome ?? '—' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 hidden lg:table-cell text-sm font-semibold text-green-700">
                            {{ isset($promocao->novo_salario) ? 'R$ ' . number_format($promocao->novo_salario, 2, ',', '.') : '—' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ \Carbon\Carbon::parse($promocao->data)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('promocoes.edit', $promocao) }}"
                                   class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded transition">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('promocoes.destroy', $promocao) }}"
                                      x-data @submit.prevent="if(confirm('Excluir esta promoção?')) $el.submit()">
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
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-400">Nenhuma promoção registrada</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if(isset($promocoes) && $promocoes->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">{{ $promocoes->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
