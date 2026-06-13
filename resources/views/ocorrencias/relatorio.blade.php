@extends('layouts.app')

@section('title', 'Relatório de Ocorrências')
@section('page-title', 'Relatório de Ocorrências')

@section('content')
<div class="space-y-4 py-4">

    <form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 grid grid-cols-1 sm:grid-cols-5 gap-3">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">De</label>
            <input type="date" name="data_inicio" value="{{ request('data_inicio') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Até</label>
            <input type="date" name="data_fim" value="{{ request('data_fim') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Empresa</label>
            <select name="empresa_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Todas</option>
                @foreach($empresas as $e)<option value="{{ $e->id }}" {{ request('empresa_id') == $e->id ? 'selected' : '' }}>{{ $e->nome }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Tipo</label>
            <select name="tipo_ocorrencia_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Todos</option>
                @foreach($tiposOcorrencias as $t)<option value="{{ $t->id }}" {{ request('tipo_ocorrencia_id') == $t->id ? 'selected' : '' }}>{{ $t->nome }}</option>@endforeach
            </select>
        </div>
        <div class="flex items-end">
            <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition w-full">Gerar</button>
        </div>
    </form>

    {{-- Cards de resumo --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm text-gray-500">Total de ocorrências</p>
            <p class="mt-1 text-3xl font-bold text-gray-900">{{ $total }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm text-gray-500">Tempo total de atraso</p>
            <p class="mt-1 text-3xl font-bold text-amber-600">{{ (int) $totalAtraso }} <span class="text-base font-medium">min</span></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <p class="text-sm text-gray-500 mb-2">Por gravidade</p>
            <div class="flex flex-wrap gap-2">
                @forelse($porGravidade as $grav => $qtd)
                    @php $cor = ['leve' => 'green', 'media' => 'yellow', 'grave' => 'red', 'gravissima' => 'purple'][$grav] ?? 'gray'; @endphp
                    <x-ui.badge :color="$cor">{{ $grav ? ucfirst($grav) : 'Sem' }}: {{ $qtd }}</x-ui.badge>
                @empty
                    <span class="text-sm text-gray-400">—</span>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Por tipo --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200"><h3 class="text-base font-semibold text-gray-900">Por tipo de ocorrência</h3></div>
            <table class="min-w-full divide-y divide-gray-100">
                <tbody class="divide-y divide-gray-50">
                    @forelse($porTipo as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 text-sm text-gray-900">{{ optional($row->tipoOcorrencia)->nome ?? '—' }}</td>
                            <td class="px-6 py-3 text-right text-sm font-semibold text-indigo-600">{{ $row->total }}</td>
                        </tr>
                    @empty
                        <tr><td class="px-6 py-8 text-center text-sm text-gray-400">Nenhum dado no período</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Por colaborador --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200"><h3 class="text-base font-semibold text-gray-900">Top colaboradores</h3></div>
            <table class="min-w-full divide-y divide-gray-100">
                <tbody class="divide-y divide-gray-50">
                    @forelse($porColaborador as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 text-sm text-gray-900">{{ optional($row->colaborador)->nome ?? '—' }}</td>
                            <td class="px-6 py-3 text-right text-sm font-semibold text-indigo-600">{{ $row->total }}</td>
                        </tr>
                    @empty
                        <tr><td class="px-6 py-8 text-center text-sm text-gray-400">Nenhum dado no período</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
