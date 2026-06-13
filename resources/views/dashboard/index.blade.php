@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6 py-4">

    {{-- Saudação --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-6 sm:px-8 sm:py-7 text-white shadow-sm">
        <div class="relative z-10">
            <h2 class="text-xl sm:text-2xl font-bold">Olá, {{ auth()->user()->name }} 👋</h2>
            <p class="mt-1 text-sm text-indigo-100">{{ now()->translatedFormat('l, d \d\e F \d\e Y') }}</p>
        </div>
        <svg class="absolute -right-6 -top-6 h-40 w-40 text-white/10" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
    </div>

    {{-- Cards de Estatísticas --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        @php
            $cards = [
                ['label' => 'Colaboradores', 'value' => $totalColaboradores, 'sub' => $colaboradoresAtivos.' ativos', 'grad' => 'from-indigo-500 to-indigo-600',
                 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ['label' => 'Empresas', 'value' => $totalEmpresas, 'sub' => 'grupos ativos', 'grad' => 'from-sky-500 to-blue-600',
                 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                ['label' => 'Ocorrências no mês', 'value' => $ocorrenciasMes, 'sub' => now()->translatedFormat('F Y'), 'grad' => 'from-orange-500 to-amber-600',
                 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                ['label' => 'Horas extras (mês)', 'value' => rtrim(rtrim(number_format($horasExtrasMes, 1, ',', '.'), '0'), ',').'h', 'sub' => 'lançadas', 'grad' => 'from-violet-500 to-purple-600',
                 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ];
        @endphp
        @foreach($cards as $c)
            <div class="group bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ $c['label'] }}</p>
                        <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900">{{ $c['value'] }}</p>
                        <p class="mt-1 text-xs text-gray-400">{{ $c['sub'] }}</p>
                    </div>
                    <div class="h-11 w-11 rounded-xl bg-gradient-to-br {{ $c['grad'] }} flex items-center justify-center shrink-0 shadow-sm">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $c['icon'] }}"/>
                        </svg>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Gráficos --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 lg:col-span-2">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Ocorrências por mês</h3>
            <canvas id="chartOcorrencias" height="120"></canvas>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Ativos por empresa</h3>
            <canvas id="chartEmpresas" height="160"></canvas>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 lg:col-span-2">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Horas extras por mês</h3>
            <canvas id="chartHoras" height="120"></canvas>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-100"><h3 class="text-base font-semibold text-gray-900">Ranking de ocorrências ({{ now()->year }})</h3></div>
            <div class="divide-y divide-gray-100">
                @forelse($rankingOcorrencias as $i => $r)
                    <div class="flex items-center gap-3 px-5 py-3">
                        <span class="h-7 w-7 shrink-0 rounded-full flex items-center justify-center text-xs font-bold {{ $i === 0 ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500' }}">{{ $i + 1 }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ optional($r->colaborador)->nome ?? '—' }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ optional(optional($r->colaborador)->empresa)->nome ?? '' }}</p>
                        </div>
                        <span class="text-sm font-semibold text-orange-600">{{ $r->total }}</span>
                    </div>
                @empty
                    <p class="px-5 py-6 text-center text-sm text-gray-400">Sem ocorrências no ano.</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Últimas Ocorrências --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Últimas Ocorrências</h3>
                <a href="{{ route('ocorrencias.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Ver todas</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($ultimasOcorrencias as $ocorrencia)
                    <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="h-9 w-9 rounded-full bg-orange-100 flex items-center justify-center shrink-0">
                                <svg class="h-4 w-4 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ optional($ocorrencia->colaborador)->nome ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ optional($ocorrencia->tipoOcorrencia)->nome ?? ($ocorrencia->titulo ?: 'Ocorrência') }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-400 shrink-0">{{ optional($ocorrencia->data_ocorrencia)->format('d/m/Y') }}</span>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center text-sm text-gray-400">Nenhuma ocorrência registrada</div>
                @endforelse
            </div>
        </div>

        {{-- Colaboradores Recentes --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Colaboradores Recentes</h3>
                <a href="{{ route('colaboradores.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Ver todos</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($colaboradoresRecentes as $colaborador)
                    @php
                        $badge = match($colaborador->status) {
                            'ATIVO'   => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                            'INATIVO' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
                            'FERIAS'  => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                            'LICENCA' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                            default   => 'bg-gray-100 text-gray-700 ring-gray-500/20',
                        };
                    @endphp
                    <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            @if($colaborador->foto)
                                <img src="{{ Storage::url($colaborador->foto) }}" class="h-9 w-9 rounded-full object-cover">
                            @else
                                <div class="h-9 w-9 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center shrink-0">
                                    <span class="text-white text-xs font-semibold">{{ strtoupper(substr($colaborador->nome, 0, 2)) }}</span>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $colaborador->nome }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ optional($colaborador->cargo)->nome ?? 'Sem cargo' }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset shrink-0 {{ $badge }}">{{ $colaborador->status }}</span>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center text-sm text-gray-400">Nenhum colaborador cadastrado</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Ações Rápidas --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Ações Rápidas</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @php
                $acoes = [
                    ['route' => 'colaboradores.create', 'label' => 'Novo Colaborador', 'color' => 'indigo', 'icon' => 'M12 4v16m8-8H4'],
                    ['route' => 'ocorrencias.create',  'label' => 'Registrar Ocorrência', 'color' => 'orange', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                    ['route' => 'horas-extras.create', 'label' => 'Lançar Hora Extra', 'color' => 'violet', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['route' => 'fechamentos.create',  'label' => 'Novo Fechamento', 'color' => 'emerald', 'icon' => 'M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z'],
                ];
                $ring = ['indigo' => 'hover:border-indigo-300 hover:bg-indigo-50', 'orange' => 'hover:border-orange-300 hover:bg-orange-50', 'violet' => 'hover:border-violet-300 hover:bg-violet-50', 'emerald' => 'hover:border-emerald-300 hover:bg-emerald-50'];
                $chip = ['indigo' => 'bg-indigo-100 text-indigo-600', 'orange' => 'bg-orange-100 text-orange-600', 'violet' => 'bg-violet-100 text-violet-600', 'emerald' => 'bg-emerald-100 text-emerald-600'];
            @endphp
            @foreach($acoes as $a)
                <a href="{{ route($a['route']) }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-200 transition group {{ $ring[$a['color']] }}">
                    <div class="h-10 w-10 rounded-xl flex items-center justify-center {{ $chip[$a['color']] }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $a['icon'] }}"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-gray-700 text-center">{{ $a['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') return;
    const dados = @json($charts);
    const fmt = { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } };

    const c1 = document.getElementById('chartOcorrencias');
    if (c1) new Chart(c1, { type: 'bar', data: { labels: dados.meses, datasets: [{ data: dados.ocorrenciasMes, backgroundColor: '#f97316', borderRadius: 6 }] }, options: fmt });

    const c2 = document.getElementById('chartHoras');
    if (c2) new Chart(c2, { type: 'line', data: { labels: dados.meses, datasets: [{ data: dados.horasMes, borderColor: '#7c3aed', backgroundColor: 'rgba(124,58,237,.1)', fill: true, tension: .3 }] }, options: fmt });

    const c3 = document.getElementById('chartEmpresas');
    if (c3) new Chart(c3, { type: 'doughnut', data: { labels: dados.empresasLabels, datasets: [{ data: dados.empresasValores, backgroundColor: ['#4f46e5','#0ea5e9','#10b981','#f59e0b','#ef4444','#8b5cf6'] }] }, options: { responsive: true, plugins: { legend: { position: 'bottom' } } } });
});
</script>
@endpush
