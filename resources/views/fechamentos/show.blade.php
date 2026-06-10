@extends('layouts.app')

@section('title', 'Fechamento')
@section('page-title', 'Detalhes do Fechamento')

@section('content')
<div class="space-y-6 py-4">

    <div class="flex items-center gap-3">
        <a href="{{ route('fechamentos.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div class="flex-1">
            <h2 class="text-lg font-semibold text-gray-900">
                Fechamento: {{ $fechamento->empresa->nome ?? '—' }} — {{ str_pad($fechamento->mes ?? '', 2, '0', STR_PAD_LEFT) }}/{{ $fechamento->ano ?? '' }}
            </h2>
        </div>
        @if(($fechamento->status ?? '') !== 'fechado')
        <form method="POST" action="{{ route('fechamentos.fechar', $fechamento) }}"
              x-data @submit.prevent="if(confirm('Confirmar fechamento? Após fechado não poderá ser alterado.')) $el.submit()">
            @csrf
            <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                Fechar Competência
            </button>
        </form>
        @endif
    </div>

    <!-- Resumo -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'Colaboradores', 'value' => $fechamento->total_colaboradores ?? 0, 'color' => 'indigo'],
            ['label' => 'Ocorrências', 'value' => $fechamento->total_ocorrencias ?? 0, 'color' => 'orange'],
            ['label' => 'Horas Extras', 'value' => ($fechamento->total_horas_extras ?? 0) . 'h', 'color' => 'purple'],
            ['label' => 'Promoções', 'value' => $fechamento->total_promocoes ?? 0, 'color' => 'green'],
        ] as $stat)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
            <p class="text-sm text-gray-500">{{ $stat['label'] }}</p>
            <p class="text-2xl font-bold text-{{ $stat['color'] }}-600 mt-1">{{ $stat['value'] }}</p>
        </div>
        @endforeach
    </div>

    <!-- Colaboradores incluídos -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-900">Colaboradores no Fechamento</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Cargo</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Ocorrências</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Horas Extras</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($fechamento->colaboradores ?? [] as $col)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $col->nome }}</td>
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $col->cargo->nome ?? '—' }}</td>
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $col->pivot->ocorrencias ?? 0 }}</td>
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $col->pivot->horas_extras ?? 0 }}h</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-6 text-center text-sm text-gray-400">Nenhum colaborador</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
