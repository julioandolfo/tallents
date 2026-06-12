@extends('layouts.app')

@section('title', 'PDI')
@section('page-title', 'Planos de Desenvolvimento (PDI)')

@section('content')
<div class="space-y-4 py-4">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $pdis->total() }} PDI(s)</p>
        <a href="{{ route('pdis.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Novo PDI
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Colaborador</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Plano</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-48">Progresso</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pdis as $pdi)
                    @php
                        $total = $pdi->acoes_count ?? 0;
                        $feitas = $pdi->acoes_concluidas_count ?? 0;
                        $pct = $total > 0 ? (int) round($feitas / $total * 100) : 0;
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ optional($pdi->colaborador)->nome ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $pdi->titulo }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden"><div class="h-full bg-emerald-500 rounded-full" style="width: {{ $pct }}%"></div></div>
                                <span class="text-xs text-gray-500">{{ $feitas }}/{{ $total }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4"><x-ui.badge :color="$pdi->statusCor()">{{ $pdi->statusLabel() }}</x-ui.badge></td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('pdis.show', $pdi) }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Abrir</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-12 text-center text-sm text-gray-400">Nenhum PDI</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($pdis->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">{{ $pdis->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
