@extends('layouts.portal')

@section('title', 'Meu Painel')

@section('content')
<div class="space-y-6">

    {{-- Saudação --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-6 text-white shadow-sm">
        <h2 class="text-xl sm:text-2xl font-bold">Olá, {{ $usuario->name }} 👋</h2>
        <p class="mt-1 text-sm text-indigo-100">{{ now()->translatedFormat('l, d \d\e F \d\e Y') }}</p>
    </div>

    @if($colaborador)
        {{-- Meus dados --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <p class="text-sm text-gray-500">Cargo</p>
                <p class="mt-1 font-semibold text-gray-900">{{ optional($colaborador->cargo)->nome ?? '—' }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <p class="text-sm text-gray-500">Setor</p>
                <p class="mt-1 font-semibold text-gray-900">{{ optional($colaborador->setor)->nome ?? '—' }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <p class="text-sm text-gray-500">Horas extras (mês)</p>
                <p class="mt-1 font-semibold text-gray-900">{{ rtrim(rtrim(number_format($horasExtrasMes, 1, ',', '.'), '0'), ',') }}h</p>
            </div>
        </div>

        {{-- Minhas ocorrências --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Minhas Ocorrências Recentes</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($ocorrencias as $oc)
                    <div class="px-6 py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ optional($oc->tipoOcorrencia)->nome ?? ($oc->titulo ?: 'Ocorrência') }}</p>
                            <p class="text-xs text-gray-500">{{ $oc->descricao ?: '—' }}</p>
                        </div>
                        <span class="text-xs text-gray-400">{{ optional($oc->data_ocorrencia)->format('d/m/Y') }}</span>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-sm text-gray-400">Nenhuma ocorrência registrada</div>
                @endforelse
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
            <p class="text-sm text-gray-500">Sua conta ainda não está vinculada a um cadastro de colaborador. Fale com o RH.</p>
        </div>
    @endif

    {{-- Em breve (próximos módulos do portal) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Em breve no seu portal</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @foreach([
                ['Mural', 'M7 8h10M7 12h6m-6 4h10M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z'],
                ['Comunicados', 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z'],
                ['Loja de Pontos', 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
                ['Meu PDI', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2'],
            ] as [$label, $icon])
                <div class="flex flex-col items-center gap-2 p-4 rounded-xl border border-dashed border-gray-200 text-gray-400">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="{{ $icon }}"/>
                    </svg>
                    <span class="text-xs text-center">{{ $label }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
