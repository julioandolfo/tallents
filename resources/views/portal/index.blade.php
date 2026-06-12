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

    {{-- Mural + agenda --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Comunicados</h3>
                <a href="{{ route('portal.mural') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Ver mural →</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($comunicados as $com)
                    <div class="px-6 py-4">
                        <div class="flex items-center gap-2 mb-1">
                            <x-ui.badge :color="$com->cor()">{{ $com->categoriaLabel() }}</x-ui.badge>
                            <span class="text-xs text-gray-400">{{ optional($com->publicado_em ?? $com->created_at)->format('d/m/Y') }}</span>
                        </div>
                        <p class="text-sm font-medium text-gray-900">{{ $com->titulo }}</p>
                        <p class="text-sm text-gray-600 line-clamp-2">{{ \Illuminate\Support\Str::limit(strip_tags($com->conteudo), 140) }}</p>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-sm text-gray-400">Nenhum comunicado por enquanto</div>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100"><h3 class="text-base font-semibold text-gray-900">Próximos eventos</h3></div>
            <div class="divide-y divide-gray-100">
                @forelse($eventos as $ev)
                    <div class="px-6 py-3 flex items-start gap-3">
                        <div class="flex-shrink-0 w-11 text-center">
                            <div class="text-lg font-bold text-gray-900 leading-none">{{ $ev->inicio->format('d') }}</div>
                            <div class="text-[11px] uppercase text-gray-500">{{ $ev->inicio->translatedFormat('M') }}</div>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $ev->titulo }}</p>
                            <p class="text-xs text-gray-500">{{ $ev->inicio->format('H:i') }}{{ $ev->local ? ' · ' . $ev->local : '' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-sm text-gray-400">Nenhum evento agendado</div>
                @endforelse
            </div>
        </div>
    </div>

    @if($colaborador && $feedbacks->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100"><h3 class="text-base font-semibold text-gray-900">Meus feedbacks</h3></div>
            <div class="divide-y divide-gray-100">
                @foreach($feedbacks as $fb)
                    <div class="px-6 py-4">
                        <div class="flex items-center gap-2 mb-1">
                            <x-ui.badge :color="$fb->cor()">{{ $fb->tipoLabel() }}</x-ui.badge>
                            <span class="text-xs text-gray-400">{{ optional($fb->data)->format('d/m/Y') }}</span>
                        </div>
                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $fb->mensagem }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
