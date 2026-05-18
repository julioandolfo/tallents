@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6 py-4">

    {{-- Cards de Estatísticas --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <!-- Total Colaboradores -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total de Colaboradores</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalColaboradores ?? 0 }}</p>
                    <p class="text-xs text-green-600 mt-2">
                        <span class="font-medium">{{ $colaboradoresAtivos ?? 0 }}</span> ativos
                    </p>
                </div>
                <div class="h-12 w-12 bg-indigo-100 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Empresas -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Empresas Cadastradas</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalEmpresas ?? 0 }}</p>
                    <p class="text-xs text-gray-400 mt-2">grupos empresariais</p>
                </div>
                <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Ocorrências do Mês -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Ocorrências no Mês</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $ocorrenciasMes ?? 0 }}</p>
                    <p class="text-xs text-orange-500 mt-2">{{ now()->translatedFormat('F Y') }}</p>
                </div>
                <div class="h-12 w-12 bg-orange-100 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Horas Extras -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Horas Extras (Mês)</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $horasExtrasMes ?? 0 }}h</p>
                    <p class="text-xs text-gray-400 mt-2">aguardando aprovação</p>
                </div>
                <div class="h-12 w-12 bg-purple-100 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Grade inferior --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Últimas Ocorrências --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Últimas Ocorrências</h3>
                <a href="{{ route('ocorrencias.index') }}"
                   class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Ver todas</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($ultimasOcorrencias ?? [] as $ocorrencia)
                    <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-orange-100 flex items-center justify-center shrink-0">
                                <svg class="h-4 w-4 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $ocorrencia->colaborador->nome ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">{{ $ocorrencia->tipoOcorrencia->nome ?? 'Sem tipo' }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-400">{{ $ocorrencia->created_at->format('d/m/Y') }}</span>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center">
                        <svg class="h-10 w-10 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-gray-400">Nenhuma ocorrência registrada</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Colaboradores Recentes --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Colaboradores Recentes</h3>
                <a href="{{ route('colaboradores.index') }}"
                   class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Ver todos</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($colaboradoresRecentes ?? [] as $colaborador)
                    <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50">
                        <div class="flex items-center gap-3">
                            @if($colaborador->foto)
                                <img src="{{ Storage::url($colaborador->foto) }}"
                                     class="h-8 w-8 rounded-full object-cover">
                            @else
                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                                    <span class="text-indigo-700 text-xs font-semibold">{{ strtoupper(substr($colaborador->nome, 0, 2)) }}</span>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $colaborador->nome }}</p>
                                <p class="text-xs text-gray-500">{{ $colaborador->cargo->nome ?? 'Sem cargo' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            @php
                                $statusColors = ['ATIVO' => 'green', 'INATIVO' => 'red', 'FERIAS' => 'yellow', 'LICENCA' => 'blue'];
                                $color = $statusColors[$colaborador->status] ?? 'gray';
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800">
                                {{ $colaborador->status }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center">
                        <svg class="h-10 w-10 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p class="text-sm text-gray-400">Nenhum colaborador cadastrado</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Atalhos Rápidos --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Ações Rápidas</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <a href="{{ route('colaboradores.create') }}"
               class="flex flex-col items-center gap-2 p-4 rounded-lg border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition group">
                <div class="h-10 w-10 bg-indigo-100 group-hover:bg-indigo-200 rounded-lg flex items-center justify-center">
                    <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-gray-700 text-center">Novo Colaborador</span>
            </a>
            <a href="{{ route('ocorrencias.create') }}"
               class="flex flex-col items-center gap-2 p-4 rounded-lg border border-gray-200 hover:border-orange-300 hover:bg-orange-50 transition group">
                <div class="h-10 w-10 bg-orange-100 group-hover:bg-orange-200 rounded-lg flex items-center justify-center">
                    <svg class="h-5 w-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-gray-700 text-center">Registrar Ocorrência</span>
            </a>
            <a href="{{ route('horas-extras.create') }}"
               class="flex flex-col items-center gap-2 p-4 rounded-lg border border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition group">
                <div class="h-10 w-10 bg-purple-100 group-hover:bg-purple-200 rounded-lg flex items-center justify-center">
                    <svg class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-gray-700 text-center">Lançar Hora Extra</span>
            </a>
            <a href="{{ route('fechamentos.create') }}"
               class="flex flex-col items-center gap-2 p-4 rounded-lg border border-gray-200 hover:border-green-300 hover:bg-green-50 transition group">
                <div class="h-10 w-10 bg-green-100 group-hover:bg-green-200 rounded-lg flex items-center justify-center">
                    <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-gray-700 text-center">Novo Fechamento</span>
            </a>
        </div>
    </div>

</div>
@endsection
