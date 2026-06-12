@extends('layouts.portal')

@section('title', 'Mural')

@section('content')
<div class="space-y-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-6 text-white shadow-sm">
        <h2 class="text-xl sm:text-2xl font-bold">Mural de Comunicados</h2>
        <p class="mt-1 text-sm text-indigo-100">Fique por dentro das novidades da empresa</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Comunicados --}}
        <div class="lg:col-span-2 space-y-3">
            @forelse($comunicados as $com)
                <div class="bg-white rounded-xl shadow-sm border {{ $com->destaque ? 'border-indigo-300 ring-1 ring-indigo-100' : 'border-gray-200' }} p-5">
                    <div class="flex items-center gap-2 mb-2">
                        <x-ui.badge :color="$com->cor()">{{ $com->categoriaLabel() }}</x-ui.badge>
                        @if($com->destaque)<x-ui.badge color="indigo">Destaque</x-ui.badge>@endif
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">{{ $com->titulo }}</h3>
                    <div class="mt-2 text-sm text-gray-700 whitespace-pre-line leading-relaxed">{{ $com->conteudo }}</div>
                    <p class="mt-3 text-xs text-gray-400">
                        {{ optional($com->autor)->name ?? 'Sistema' }} · {{ optional($com->publicado_em ?? $com->created_at)->format('d/m/Y H:i') }}
                    </p>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-12 text-center text-sm text-gray-400">Nenhum comunicado por enquanto</div>
            @endforelse

            @if($comunicados->hasPages())
                <div>{{ $comunicados->links() }}</div>
            @endif
        </div>

        {{-- Próximos eventos --}}
        <div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-100"><h3 class="text-base font-semibold text-gray-900">Próximos eventos</h3></div>
                <div class="divide-y divide-gray-100">
                    @forelse($eventos as $ev)
                        <div class="px-5 py-3 flex items-start gap-3">
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
                        <div class="px-5 py-8 text-center text-sm text-gray-400">Nenhum evento agendado</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
