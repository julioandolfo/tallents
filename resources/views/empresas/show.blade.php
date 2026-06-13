@extends('layouts.app')

@section('title', $empresa->nome ?? 'Empresa')
@section('page-title', 'Detalhes da Empresa')

@section('content')
@php
    $tabs = [
        'geral'         => ['label' => 'Visão Geral',   'count' => null],
        'colaboradores' => ['label' => 'Colaboradores', 'count' => $empresa->colaboradores_count ?? $empresa->colaboradores->count()],
        'setores'       => ['label' => 'Setores',       'count' => $empresa->setores_count ?? $empresa->setores->count()],
        'cargos'        => ['label' => 'Cargos',         'count' => $empresa->cargos_count ?? $empresa->cargos->count()],
    ];
@endphp

<div class="space-y-6 py-4" x-data="{ tab: 'geral' }">

    <div class="flex items-center gap-3">
        <a href="{{ route('empresas.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div class="flex-1"></div>
        <a href="{{ route('empresas.edit', $empresa) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
            Editar
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    {{-- Identificação + Abas --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 pt-6">
            <div class="flex items-start gap-4">
                <div class="h-14 w-14 shrink-0 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-lg font-bold">
                    {{ strtoupper(substr($empresa->nome ?? 'E', 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-3">
                        <h2 class="text-xl font-bold text-gray-900 truncate">{{ $empresa->nome }}</h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ring-1 ring-inset {{ $empresa->ativa ? 'bg-green-100 text-green-700 ring-green-600/20' : 'bg-gray-100 text-gray-600 ring-gray-600/20' }}">
                            {{ $empresa->ativa ? 'Ativa' : 'Inativa' }}
                        </span>
                    </div>
                    @if($empresa->razao_social)
                        <p class="text-sm text-gray-500 mt-0.5">{{ $empresa->razao_social }}</p>
                    @endif
                </div>
            </div>

            <nav class="flex gap-1 overflow-x-auto mt-5 -mb-px border-t border-gray-100 pt-1">
                @foreach($tabs as $key => $t)
                    <button type="button" @click="tab = '{{ $key }}'"
                            :class="tab === '{{ $key }}' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 transition">
                        {{ $t['label'] }}
                        @if(!is_null($t['count']) && $t['count'] > 0)
                            <span class="text-xs bg-gray-100 text-gray-600 rounded-full px-2 py-0.5">{{ $t['count'] }}</span>
                        @endif
                    </button>
                @endforeach
            </nav>
        </div>
    </div>

    {{-- ═══════════ ABA: VISÃO GERAL ═══════════ --}}
    <div x-show="tab === 'geral'" x-cloak class="space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500">Colaboradores</p>
                <p class="text-2xl font-bold text-indigo-600">{{ $empresa->colaboradores_count ?? $empresa->colaboradores->count() }}</p>
                <p class="text-xs text-gray-400">{{ $colaboradoresAtivos ?? 0 }} ativos</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500">Setores</p>
                <p class="text-2xl font-bold text-sky-600">{{ $empresa->setores_count ?? $empresa->setores->count() }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-sm text-gray-500">Cargos</p>
                <p class="text-2xl font-bold text-emerald-600">{{ $empresa->cargos_count ?? $empresa->cargos->count() }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200"><h3 class="text-base font-semibold text-gray-900">Dados da Empresa</h3></div>
            <dl class="px-6 py-4 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3 text-sm">
                <div class="flex justify-between border-b border-gray-50 py-1"><dt class="text-gray-500">CNPJ</dt><dd class="text-gray-900 font-medium">{{ $empresa->cnpj ?: '—' }}</dd></div>
                <div class="flex justify-between border-b border-gray-50 py-1"><dt class="text-gray-500">E-mail</dt><dd class="text-gray-900 font-medium">{{ $empresa->email ?: '—' }}</dd></div>
                <div class="flex justify-between border-b border-gray-50 py-1"><dt class="text-gray-500">Telefone</dt><dd class="text-gray-900 font-medium">{{ $empresa->telefone ?: '—' }}</dd></div>
                <div class="flex justify-between border-b border-gray-50 py-1"><dt class="text-gray-500">Site</dt><dd class="text-gray-900 font-medium">{{ $empresa->site ?: '—' }}</dd></div>
                <div class="flex justify-between border-b border-gray-50 py-1"><dt class="text-gray-500">Cidade/UF</dt><dd class="text-gray-900 font-medium">{{ trim(($empresa->cidade ?: '—') . ' / ' . ($empresa->estado ?: '—'), ' /') }}</dd></div>
                <div class="flex justify-between border-b border-gray-50 py-1"><dt class="text-gray-500">% Hora Extra</dt><dd class="text-gray-900 font-medium">{{ $empresa->percentual_hora_extra }}%</dd></div>
            </dl>
        </div>
    </div>

    {{-- ═══════════ ABA: COLABORADORES ═══════════ --}}
    <div x-show="tab === 'colaboradores'" x-cloak>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Colaboradores</h3>
                <a href="{{ route('colaboradores.create') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">+ Novo colaborador</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50"><tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Cargo</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Setor</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3"></th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($empresa->colaboradores as $colab)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $colab->nome }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ optional($colab->cargo)->nome ?? '—' }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ optional($colab->setor)->nome ?? '—' }}</td>
                                <td class="px-6 py-3 text-sm">
                                    @php $cor = ['ATIVO' => 'text-emerald-600', 'INATIVO' => 'text-red-600'][$colab->status] ?? 'text-gray-500'; @endphp
                                    <span class="{{ $cor }}">{{ ucfirst(strtolower($colab->status)) }}</span>
                                </td>
                                <td class="px-6 py-3 text-right"><a href="{{ route('colaboradores.show', $colab) }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Ver</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-sm text-gray-400">Nenhum colaborador nesta empresa</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ═══════════ ABA: SETORES ═══════════ --}}
    <div x-show="tab === 'setores'" x-cloak>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Setores</h3>
                <a href="{{ route('setores.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Gerenciar setores</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50"><tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nome</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Colaboradores</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($empresa->setores as $setor)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $setor->nome }}</td>
                                <td class="px-6 py-3 text-right text-sm text-gray-600">{{ $setor->colaboradores_count ?? 0 }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="px-6 py-8 text-center text-sm text-gray-400">Nenhum setor cadastrado</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ═══════════ ABA: CARGOS ═══════════ --}}
    <div x-show="tab === 'cargos'" x-cloak>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Cargos</h3>
                <a href="{{ route('cargos.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Gerenciar cargos</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50"><tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nome</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Salário Base</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Colaboradores</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($empresa->cargos as $cargo)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $cargo->nome }}</td>
                                <td class="px-6 py-3 text-right text-sm text-gray-600">{{ isset($cargo->salario_base) ? 'R$ ' . number_format($cargo->salario_base, 2, ',', '.') : '—' }}</td>
                                <td class="px-6 py-3 text-right text-sm text-gray-600">{{ $cargo->colaboradores_count ?? 0 }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-8 text-center text-sm text-gray-400">Nenhum cargo cadastrado</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
