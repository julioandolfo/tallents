@extends('layouts.app')

@section('title', $colaborador->nome ?? 'Colaborador')
@section('page-title', 'Perfil do Colaborador')

@section('content')
@php
    $cntOcorrencias = ($colaborador->ocorrencias_count ?? $colaborador->ocorrencias->count()) + $colaborador->advertencias->count();
    $cntHoras       = ($colaborador->horas_extras_count ?? $colaborador->horasExtras->count()) + $colaborador->bonus->count() + $colaborador->movimentacoesBanco->count();
    $cntCarreira    = ($colaborador->promocoes_count ?? $colaborador->promocoes->count()) + $colaborador->avaliacoes->count() + $colaborador->pdis->count() + $colaborador->onboardings->count() + $colaborador->feedbacks->count();
    $cntPessoal     = $colaborador->dependentes->count() + $colaborador->formacoes->count();
    $cntContratos   = $colaborador->contratos_count ?? $colaborador->contratos->count();

    $tabs = [
        'geral'       => ['label' => 'Visão Geral',  'count' => null],
        'ocorrencias' => ['label' => 'Ocorrências',  'count' => $cntOcorrencias],
        'horas'       => ['label' => 'Horas & Bônus','count' => $cntHoras],
        'carreira'    => ['label' => 'Carreira',     'count' => $cntCarreira],
        'pessoal'     => ['label' => 'Pessoal',      'count' => $cntPessoal],
        'contratos'   => ['label' => 'Contratos',    'count' => $cntContratos],
    ];
@endphp

<div class="space-y-6 py-4" x-data="{ tab: 'geral' }">

    {{-- Ações --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('colaboradores.index') }}"
           class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div class="flex-1"></div>
        <a href="{{ route('colaboradores.edit', $colaborador) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Editar
        </a>
        <form method="POST" action="{{ route('colaboradores.destroy', $colaborador) }}"
              x-data
              @submit.prevent="if(confirm('Tem certeza que deseja excluir este colaborador?')) $el.submit()">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Excluir
            </button>
        </form>
    </div>

    {{-- Card Principal - Identificação + Abas --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 h-20"></div>
        <div class="px-6">
            {{-- Foto (única que sobrepõe o banner) --}}
            <div class="-mt-10">
                @if($colaborador->foto ?? false)
                    <img src="{{ Storage::url($colaborador->foto) }}"
                         class="h-20 w-20 rounded-full object-cover ring-4 ring-white shadow">
                @else
                    <div class="h-20 w-20 rounded-full bg-indigo-100 ring-4 ring-white shadow flex items-center justify-center">
                        <span class="text-indigo-700 text-xl font-bold">{{ strtoupper(substr($colaborador->nome ?? 'N', 0, 2)) }}</span>
                    </div>
                @endif
            </div>

            {{-- Nome + metadados (abaixo do banner, sobre o fundo branco) --}}
            <div class="mt-3">
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="text-xl font-bold text-gray-900">{{ $colaborador->nome ?? 'N/A' }}</h2>
                    @php
                        $badges = [
                            'ATIVO'   => 'bg-green-100 text-green-700 ring-green-600/20',
                            'INATIVO' => 'bg-red-100 text-red-700 ring-red-600/20',
                            'FERIAS'  => 'bg-yellow-100 text-yellow-700 ring-yellow-600/20',
                            'LICENCA' => 'bg-blue-100 text-blue-700 ring-blue-600/20',
                        ];
                        $badge = $badges[$colaborador->status ?? ''] ?? 'bg-gray-100 text-gray-700 ring-gray-600/20';
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ring-1 ring-inset {{ $badge }}">
                        {{ $colaborador->status ?? 'N/A' }}
                    </span>
                </div>
                <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1.5 text-sm text-gray-500">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ $colaborador->cargo->nome ?? 'Sem cargo' }}
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        {{ $colaborador->empresa->nome ?? 'Sem empresa' }}
                    </span>
                    @if($colaborador->setor ?? false)
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                        </svg>
                        {{ $colaborador->setor->nome }}
                    </span>
                    @endif
                </div>
            </div>

            {{-- Navegação por abas --}}
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

    {{-- ════════════════════ ABA: VISÃO GERAL ════════════════════ --}}
    <div x-show="tab === 'geral'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Dados Pessoais --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Dados Pessoais</h3>
            </div>
            <dl class="px-6 py-4 space-y-3">
                @foreach([
                    ['label' => 'CPF', 'value' => $colaborador->cpf ?? '—'],
                    ['label' => 'RG', 'value' => $colaborador->rg ?? '—'],
                    ['label' => 'Data de Nascimento', 'value' => isset($colaborador->data_nascimento) ? \Carbon\Carbon::parse($colaborador->data_nascimento)->format('d/m/Y') : '—'],
                    ['label' => 'Sexo', 'value' => ['M' => 'Masculino', 'F' => 'Feminino', 'O' => 'Outro'][$colaborador->sexo ?? ''] ?? '—'],
                    ['label' => 'Estado Civil', 'value' => ucfirst($colaborador->estado_civil ?? '—')],
                    ['label' => 'Telefone', 'value' => $colaborador->telefone ?? '—'],
                    ['label' => 'Celular', 'value' => $colaborador->celular ?? '—'],
                    ['label' => 'E-mail Pessoal', 'value' => $colaborador->email_pessoal ?? '—'],
                    ['label' => 'PIS/PASEP', 'value' => $colaborador->pis ?? '—'],
                    ['label' => 'CTPS', 'value' => $colaborador->ctps ?? '—'],
                ] as $item)
                <div class="flex justify-between py-1 border-b border-gray-50 last:border-0">
                    <dt class="text-sm text-gray-500">{{ $item['label'] }}</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $item['value'] }}</dd>
                </div>
                @endforeach
            </dl>
        </div>

        {{-- Dados de Contrato --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Dados de Contrato</h3>
            </div>
            <dl class="px-6 py-4 space-y-3">
                @foreach([
                    ['label' => 'Matrícula', 'value' => $colaborador->matricula ?? '—'],
                    ['label' => 'Data de Admissão', 'value' => isset($colaborador->data_admissao) ? \Carbon\Carbon::parse($colaborador->data_admissao)->format('d/m/Y') : '—'],
                    ['label' => 'Data de Demissão', 'value' => $colaborador->data_demissao ? \Carbon\Carbon::parse($colaborador->data_demissao)->format('d/m/Y') : '—'],
                    ['label' => 'Salário Base', 'value' => isset($colaborador->salario) ? 'R$ ' . number_format($colaborador->salario, 2, ',', '.') : '—'],
                    ['label' => 'Regime', 'value' => $colaborador->regime_trabalho ?? '—'],
                    ['label' => 'Carga Horária', 'value' => ($colaborador->carga_horaria ?? '—') . 'h/semana'],
                    ['label' => 'Nível Hierárquico', 'value' => $colaborador->nivelHierarquico->nome ?? '—'],
                    ['label' => 'Líder / Gestor', 'value' => $colaborador->lider->nome ?? '—'],
                    ['label' => 'Setor', 'value' => $colaborador->setor->nome ?? '—'],
                ] as $item)
                <div class="flex justify-between py-1 border-b border-gray-50 last:border-0">
                    <dt class="text-sm text-gray-500">{{ $item['label'] }}</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $item['value'] }}</dd>
                </div>
                @endforeach
            </dl>
        </div>

        {{-- Endereço --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Endereço</h3>
            </div>
            <div class="px-6 py-4">
                @if($colaborador->logradouro ?? false)
                    <p class="text-sm text-gray-900">
                        {{ $colaborador->logradouro }}, {{ $colaborador->numero }}
                        @if($colaborador->complemento) — {{ $colaborador->complemento }} @endif
                    </p>
                    <p class="text-sm text-gray-600 mt-1">{{ $colaborador->bairro }}</p>
                    <p class="text-sm text-gray-600">{{ $colaborador->cidade }}/{{ $colaborador->estado }} — CEP: {{ $colaborador->cep }}</p>
                @else
                    <p class="text-sm text-gray-400">Endereço não cadastrado</p>
                @endif
            </div>
        </div>

        {{-- Dados Bancários --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Dados Bancários e Financeiros</h3>
            </div>
            <dl class="px-6 py-4 space-y-3">
                @php $temBanco = $colaborador->banco || $colaborador->pix || $colaborador->conta || $colaborador->cnpj; @endphp
                @if($temBanco)
                    @foreach([
                        ['label' => 'Banco', 'value' => $colaborador->banco ?? '—'],
                        ['label' => 'Agência', 'value' => $colaborador->agencia ?? '—'],
                        ['label' => 'Conta', 'value' => $colaborador->conta ?? '—'],
                        ['label' => 'Tipo de Conta', 'value' => ['CORRENTE' => 'Corrente', 'POUPANCA' => 'Poupança'][$colaborador->tipo_conta ?? ''] ?? '—'],
                        ['label' => 'Chave PIX', 'value' => $colaborador->pix ?? '—'],
                        ['label' => 'CNPJ', 'value' => $colaborador->cnpj ?? '—'],
                    ] as $item)
                    <div class="flex justify-between py-1 border-b border-gray-50 last:border-0">
                        <dt class="text-sm text-gray-500">{{ $item['label'] }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $item['value'] }}</dd>
                    </div>
                    @endforeach
                @else
                    <p class="text-sm text-gray-400">Nenhum dado bancário cadastrado</p>
                @endif
            </dl>
        </div>

        {{-- Acesso ao Sistema --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Acesso ao Sistema</h3>
            </div>
            <dl class="px-6 py-4 space-y-3">
                @php $usuarioAcesso = $colaborador->user; @endphp
                @if($usuarioAcesso)
                    @foreach([
                        ['label' => 'E-mail de login', 'value' => $colaborador->email_login ?: $usuarioAcesso->email],
                        ['label' => 'Perfil de acesso', 'value' => ['ADMIN' => 'Administrador', 'RH' => 'RH', 'GESTOR' => 'Gestor', 'COLABORADOR' => 'Colaborador'][$usuarioAcesso->role] ?? $usuarioAcesso->role],
                        ['label' => 'Conta ativa', 'value' => $usuarioAcesso->ativo ? 'Sim' : 'Não'],
                        ['label' => 'Último acesso', 'value' => optional($usuarioAcesso->last_login_at)->format('d/m/Y H:i') ?? 'Nunca'],
                    ] as $item)
                    <div class="flex justify-between py-1 border-b border-gray-50 last:border-0">
                        <dt class="text-sm text-gray-500">{{ $item['label'] }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $item['value'] }}</dd>
                    </div>
                    @endforeach
                @else
                    <p class="text-sm text-gray-400">Este colaborador não possui acesso ao sistema.
                        <a href="{{ route('colaboradores.edit', $colaborador) }}" class="text-indigo-600 hover:text-indigo-700 font-medium">Conceder acesso</a>
                    </p>
                @endif
            </dl>
        </div>

        {{-- Resumo de RH --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Resumo RH</h3>
            </div>
            <div class="grid grid-cols-3 divide-x divide-gray-100">
                <div class="px-4 py-5 text-center">
                    <p class="text-2xl font-bold text-orange-600">{{ $colaborador->ocorrencias_count ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">Ocorrências</p>
                </div>
                <div class="px-4 py-5 text-center">
                    <p class="text-2xl font-bold text-purple-600">{{ $colaborador->horas_extras_count ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">Horas Extras</p>
                </div>
                <div class="px-4 py-5 text-center">
                    <p class="text-2xl font-bold text-green-600">{{ $colaborador->promocoes_count ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">Promoções</p>
                </div>
            </div>
        </div>

        {{-- Observações --}}
        @if($colaborador->observacoes)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 lg:col-span-2">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Observações</h3>
            </div>
            <div class="px-6 py-4">
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $colaborador->observacoes }}</p>
            </div>
        </div>
        @endif
    </div>

    {{-- ════════════════════ ABA: OCORRÊNCIAS ════════════════════ --}}
    <div x-show="tab === 'ocorrencias'" x-cloak class="space-y-6">

        {{-- Histórico de Ocorrências --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Ocorrências</h3>
                <a href="{{ route('ocorrencias.create', ['colaborador_id' => $colaborador->id]) }}"
                   class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nova Ocorrência
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Observação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($colaborador->ocorrencias ?? [] as $ocorrencia)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm text-gray-900">{{ $ocorrencia->tipoOcorrencia->nome ?? '—' }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ optional($ocorrencia->data_ocorrencia)->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $ocorrencia->descricao ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-6 text-center text-sm text-gray-400">Nenhuma ocorrência registrada</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Advertências --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Advertências</h3>
                <a href="{{ route('advertencias.create', ['colaborador_id' => $colaborador->id]) }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">+ Nova advertência</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50"><tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Motivo</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Suspensão</th>
                        <th class="px-6 py-3"></th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($colaborador->advertencias as $adv)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm text-gray-600">{{ optional($adv->data)->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ ucfirst(strtolower($adv->tipo ?? '—')) }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600 truncate max-w-xs">{{ $adv->motivo }}</td>
                                <td class="px-6 py-3 text-right text-sm text-gray-600">{{ $adv->dias_suspensao ? $adv->dias_suspensao.' dia(s)' : '—' }}</td>
                                <td class="px-6 py-3 text-right"><a href="{{ route('advertencias.show', $adv) }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Ver</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-6 text-center text-sm text-gray-400">Nenhuma advertência</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ════════════════════ ABA: HORAS & BÔNUS ════════════════════ --}}
    <div x-show="tab === 'horas'" x-cloak class="space-y-6">

        {{-- Horas Extras --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Horas Extras</h3>
                <a href="{{ route('horas-extras.create', ['colaborador_id' => $colaborador->id]) }}"
                   class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Lançar Hora Extra
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Horas</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($colaborador->horasExtras ?? [] as $he)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm text-gray-900">{{ optional($he->data)->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-6 py-3 text-sm text-gray-900">{{ $he->horas }}h</td>
                                <td class="px-6 py-3">
                                    @php $st = strtoupper($he->status ?? 'PENDENTE'); @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $st === 'APROVADO' ? 'bg-green-100 text-green-800' : ($st === 'PENDENTE' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst(strtolower($st)) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-6 text-center text-sm text-gray-400">Nenhuma hora extra lançada</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Banco de horas --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-gray-900">Banco de Horas</h3>
                    @php $saldo = $colaborador->saldoBancoHoras(); @endphp
                    <p class="text-xs mt-0.5 {{ $saldo >= 0 ? 'text-emerald-600' : 'text-red-600' }}">Saldo atual: {{ number_format($saldo, 2, ',', '.') }}h</p>
                </div>
                <a href="{{ route('banco-horas.extrato', $colaborador) }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Ver extrato</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50"><tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Horas</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Motivo</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($colaborador->movimentacoesBanco->take(5) as $mov)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm text-gray-600">{{ optional($mov->data)->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-6 py-3 text-sm font-medium {{ $mov->tipo === 'CREDITO' ? 'text-emerald-600' : 'text-red-600' }}">{{ $mov->tipo === 'CREDITO' ? 'Crédito' : 'Débito' }}</td>
                                <td class="px-6 py-3 text-right text-sm text-gray-900">{{ number_format((float) $mov->horas, 2, ',', '.') }}h</td>
                                <td class="px-6 py-3 text-sm text-gray-600 truncate max-w-md">{{ $mov->motivo }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-6 text-center text-sm text-gray-400">Nenhuma movimentação</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Bônus do colaborador --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200" x-data="{ open: false }">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Bônus</h3>
                <button @click="open = !open" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">+ Adicionar</button>
            </div>

            <div x-show="open" x-cloak class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <form method="POST" action="{{ route('colaboradores.bonus.store', $colaborador) }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    @csrf
                    <select name="tipo_bonus_id" required class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Tipo de bônus *</option>
                        @foreach($tiposBonus ?? [] as $tb)<option value="{{ $tb->id }}">{{ $tb->nome }}</option>@endforeach
                    </select>
                    <input type="number" step="0.01" min="0" name="valor" required placeholder="Valor (R$) *" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <input type="date" name="data_inicio" title="Início da vigência" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <input type="date" name="data_fim" title="Fim da vigência" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <input type="text" name="observacoes" placeholder="Observações" class="lg:col-span-3 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <div class="flex justify-end"><button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Salvar</button></div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Valor</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Vigência</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($colaborador->bonus as $b)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ optional($b->tipoBonus)->nome ?? '—' }}</td>
                                <td class="px-6 py-3 text-right text-sm text-gray-900">R$ {{ number_format($b->valor, 2, ',', '.') }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ optional($b->data_inicio)->format('d/m/Y') ?? '—' }} — {{ optional($b->data_fim)->format('d/m/Y') ?? 'sem fim' }}</td>
                                <td class="px-6 py-3 text-sm">{!! $b->ativo ? '<span class="text-emerald-600">Ativo</span>' : '<span class="text-gray-400">Inativo</span>' !!}</td>
                                <td class="px-6 py-3 text-right">
                                    <form method="POST" action="{{ route('colaboradores.bonus.destroy', $b) }}" x-data @submit.prevent="if(confirm('Remover bônus?')) $el.submit()">
                                        @csrf @method('DELETE')
                                        <button class="p-1.5 text-gray-400 hover:text-red-600 rounded"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-6 text-center text-sm text-gray-400">Nenhum bônus cadastrado</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ════════════════════ ABA: CARREIRA ════════════════════ --}}
    <div x-show="tab === 'carreira'" x-cloak class="space-y-6">

        {{-- Histórico de Promoções --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Histórico de Promoções</h3>
                <a href="{{ route('promocoes.create', ['colaborador_id' => $colaborador->id]) }}"
                   class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Registrar Promoção
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Cargo Anterior</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Novo Cargo</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Novo Salário</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($colaborador->promocoes ?? [] as $promocao)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm text-gray-900">{{ optional($promocao->data_promocao)->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $promocao->cargoAnterior->nome ?? '—' }}</td>
                                <td class="px-6 py-3 text-sm text-gray-900 font-medium">{{ $promocao->cargoNovo->nome ?? '—' }}</td>
                                <td class="px-6 py-3 text-sm text-green-700 font-medium">
                                    {{ isset($promocao->salario_novo) ? 'R$ ' . number_format($promocao->salario_novo, 2, ',', '.') : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-6 text-center text-sm text-gray-400">Nenhuma promoção registrada</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Avaliações de desempenho --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Avaliações de Desempenho</h3>
                <a href="{{ route('avaliacoes.create', ['colaborador_id' => $colaborador->id]) }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">+ Nova avaliação</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50"><tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Ciclo</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Avaliador</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Nota</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3"></th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($colaborador->avaliacoes as $av)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $av->ciclo ?? '—' }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ optional($av->avaliador)->name ?? '—' }}</td>
                                <td class="px-6 py-3 text-right text-sm text-gray-900">{{ $av->nota_geral !== null ? number_format($av->nota_geral, 1, ',', '.') : '—' }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ ucfirst(strtolower($av->status ?? '—')) }}</td>
                                <td class="px-6 py-3 text-right"><a href="{{ route('avaliacoes.show', $av) }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Ver</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-6 text-center text-sm text-gray-400">Nenhuma avaliação</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PDI --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Plano de Desenvolvimento (PDI)</h3>
                <a href="{{ route('pdis.create', ['colaborador_id' => $colaborador->id]) }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">+ Novo PDI</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50"><tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Título</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Prazo</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3"></th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($colaborador->pdis as $pdi)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $pdi->titulo }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ optional($pdi->prazo)->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ \App\Models\Pdi::STATUS[$pdi->status] ?? $pdi->status }}</td>
                                <td class="px-6 py-3 text-right"><a href="{{ route('pdis.show', $pdi) }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Ver</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-6 text-center text-sm text-gray-400">Nenhum PDI</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Onboarding --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Onboarding</h3>
                <a href="{{ route('onboarding.create', ['colaborador_id' => $colaborador->id]) }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">+ Novo onboarding</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50"><tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Início</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3"></th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($colaborador->onboardings as $ob)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm text-gray-600">{{ optional($ob->data_inicio)->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ \App\Models\Onboarding::STATUS[$ob->status] ?? $ob->status }}</td>
                                <td class="px-6 py-3 text-right"><a href="{{ route('onboarding.show', $ob) }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Ver</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-6 text-center text-sm text-gray-400">Nenhum onboarding</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Feedbacks --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Feedbacks</h3>
                <a href="{{ route('feedbacks.create', ['colaborador_id' => $colaborador->id]) }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">+ Novo feedback</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50"><tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Autor</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Mensagem</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($colaborador->feedbacks as $fb)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm text-gray-600">{{ optional($fb->data)->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ ucfirst(strtolower($fb->tipo ?? '—')) }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ optional($fb->autor)->name ?? '—' }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600 truncate max-w-md">{{ $fb->mensagem }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-6 text-center text-sm text-gray-400">Nenhum feedback</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ════════════════════ ABA: PESSOAL ════════════════════ --}}
    <div x-show="tab === 'pessoal'" x-cloak class="space-y-6">

        {{-- Dependentes --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200" x-data="{ open: false }">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Dependentes</h3>
                <button @click="open = !open" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">+ Adicionar</button>
            </div>

            <div x-show="open" x-cloak class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <form method="POST" action="{{ route('dependentes.store', $colaborador) }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    @csrf
                    <input type="text" name="nome" required placeholder="Nome *" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <select name="parentesco" required class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach(\App\Models\Dependente::PARENTESCOS as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                    </select>
                    <input type="date" name="data_nascimento" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <input type="text" name="cpf" placeholder="CPF" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <label class="flex items-center gap-2 text-sm text-gray-600"><input type="checkbox" name="dependente_ir" value="1" class="rounded border-gray-300 text-indigo-600"> Dependente p/ IR</label>
                    <div class="sm:col-span-2 lg:col-span-3 flex justify-end"><button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Salvar</button></div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Parentesco</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nascimento</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">IR</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($colaborador->dependentes as $dep)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $dep->nome }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $dep->parentescoLabel() }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ optional($dep->data_nascimento)->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-6 py-3 text-sm">{!! $dep->dependente_ir ? '<span class="text-emerald-600">Sim</span>' : '<span class="text-gray-400">Não</span>' !!}</td>
                                <td class="px-6 py-3 text-right">
                                    <form method="POST" action="{{ route('dependentes.destroy', $dep) }}" x-data @submit.prevent="if(confirm('Remover dependente?')) $el.submit()">
                                        @csrf @method('DELETE')
                                        <button class="p-1.5 text-gray-400 hover:text-red-600 rounded"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-6 text-center text-sm text-gray-400">Nenhum dependente cadastrado</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Formação acadêmica --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200" x-data="{ open: false }">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Formação acadêmica</h3>
                <button @click="open = !open" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">+ Adicionar</button>
            </div>

            <div x-show="open" x-cloak class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <form method="POST" action="{{ route('formacoes.store', $colaborador) }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    @csrf
                    <select name="nivel" required class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach(\App\Models\Formacao::NIVEIS as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                    </select>
                    <input type="text" name="curso" required placeholder="Curso *" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <input type="text" name="instituicao" placeholder="Instituição" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <select name="situacao" required class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach(\App\Models\Formacao::SITUACOES as $k => $v)<option value="{{ $k }}" {{ $k === 'CONCLUIDO' ? 'selected' : '' }}>{{ $v }}</option>@endforeach
                    </select>
                    <input type="number" name="ano_conclusao" min="1950" max="2100" placeholder="Ano conclusão" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <div class="sm:col-span-2 lg:col-span-3 flex justify-end"><button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Salvar</button></div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nível</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Curso</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Instituição</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Situação</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Ano</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($colaborador->formacoes as $form)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $form->nivelLabel() }}</td>
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $form->curso }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $form->instituicao ?? '—' }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $form->situacaoLabel() }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $form->ano_conclusao ?? '—' }}</td>
                                <td class="px-6 py-3 text-right">
                                    <form method="POST" action="{{ route('formacoes.destroy', $form) }}" x-data @submit.prevent="if(confirm('Remover formação?')) $el.submit()">
                                        @csrf @method('DELETE')
                                        <button class="p-1.5 text-gray-400 hover:text-red-600 rounded"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-6 text-center text-sm text-gray-400">Nenhuma formação cadastrada</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ════════════════════ ABA: CONTRATOS ════════════════════ --}}
    <div x-show="tab === 'contratos'" x-cloak class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Contratos e Documentos</h3>
                <a href="{{ route('contratos.create', ['colaborador_id' => $colaborador->id]) }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">+ Novo contrato</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Título</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Assinado em</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($colaborador->contratos as $contrato)
                            @php
                                $statusCor = [
                                    'ASSINADO'  => 'text-emerald-600',
                                    'PENDENTE'  => 'text-amber-600',
                                    'CANCELADO' => 'text-red-600',
                                ][$contrato->status] ?? 'text-gray-500';
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $contrato->titulo }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ ucfirst(strtolower($contrato->tipo)) }}</td>
                                <td class="px-6 py-3 text-sm font-medium {{ $statusCor }}">{{ ucfirst(strtolower($contrato->status)) }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ optional($contrato->assinado_em)->format('d/m/Y H:i') ?? '—' }}</td>
                                <td class="px-6 py-3 text-right">
                                    <a href="{{ route('contratos.show', $contrato) }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Ver</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-6 text-center text-sm text-gray-400">Nenhum contrato cadastrado</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
