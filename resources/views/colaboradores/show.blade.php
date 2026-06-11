@extends('layouts.app')

@section('title', $colaborador->nome ?? 'Colaborador')
@section('page-title', 'Perfil do Colaborador')

@section('content')
<div class="space-y-6 py-4">

    {{-- Header com ações --}}
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

    {{-- Card Principal - Identificação --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 h-24"></div>
        <div class="px-6 pb-6">
            <div class="flex flex-col sm:flex-row sm:items-end gap-4 -mt-12">
                <!-- Foto -->
                <div class="shrink-0">
                    @if($colaborador->foto ?? false)
                        <img src="{{ Storage::url($colaborador->foto) }}"
                             class="h-24 w-24 rounded-full object-cover ring-4 ring-white shadow">
                    @else
                        <div class="h-24 w-24 rounded-full bg-indigo-100 ring-4 ring-white shadow flex items-center justify-center">
                            <span class="text-indigo-700 text-2xl font-bold">{{ strtoupper(substr($colaborador->nome ?? 'N', 0, 2)) }}</span>
                        </div>
                    @endif
                </div>
                <!-- Info -->
                <div class="flex-1 pt-2 sm:pt-0 pb-1">
                    <div class="flex flex-wrap items-center gap-3">
                        <h2 class="text-xl font-bold text-gray-900">{{ $colaborador->nome ?? 'N/A' }}</h2>
                        @php
                            $badges = [
                                'ATIVO'   => 'bg-green-100 text-green-800',
                                'INATIVO' => 'bg-red-100 text-red-800',
                                'FERIAS'  => 'bg-yellow-100 text-yellow-800',
                                'LICENCA' => 'bg-blue-100 text-blue-800',
                            ];
                            $badge = $badges[$colaborador->status ?? ''] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                            {{ $colaborador->status ?? 'N/A' }}
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-3 mt-1 text-sm text-gray-500">
                        <span class="flex items-center gap-1">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            {{ $colaborador->cargo->nome ?? 'Sem cargo' }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                            </svg>
                            {{ $colaborador->empresa->nome ?? 'Sem empresa' }}
                        </span>
                        @if($colaborador->setor ?? false)
                        <span class="flex items-center gap-1">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/>
                            </svg>
                            {{ $colaborador->setor->nome }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Grade de informações --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

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
                    ['label' => 'E-mail Pessoal', 'value' => $colaborador->email_pessoal ?? '—'],
                    ['label' => 'PIS/PASEP', 'value' => $colaborador->pis ?? '—'],
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
                    ['label' => 'Salário Base', 'value' => isset($colaborador->salario) ? 'R$ ' . number_format($colaborador->salario, 2, ',', '.') : '—'],
                    ['label' => 'Regime', 'value' => $colaborador->regime_trabalho ?? '—'],
                    ['label' => 'Carga Horária', 'value' => ($colaborador->carga_horaria ?? '—') . 'h/semana'],
                    ['label' => 'Nível Hierárquico', 'value' => $colaborador->nivelHierarquico->nome ?? '—'],
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
    </div>

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
                            <td class="px-6 py-3 text-sm text-gray-600">{{ \Carbon\Carbon::parse($ocorrencia->data)->format('d/m/Y') }}</td>
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $ocorrencia->observacao ?? '—' }}</td>
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
                            <td class="px-6 py-3 text-sm text-gray-900">{{ \Carbon\Carbon::parse($he->data)->format('d/m/Y') }}</td>
                            <td class="px-6 py-3 text-sm text-gray-900">{{ $he->quantidade }}h</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $he->status == 'aprovado' ? 'bg-green-100 text-green-800' : ($he->status == 'pendente' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($he->status ?? 'pendente') }}
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
                            <td class="px-6 py-3 text-sm text-gray-900">{{ \Carbon\Carbon::parse($promocao->data)->format('d/m/Y') }}</td>
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $promocao->cargoAnterior->nome ?? '—' }}</td>
                            <td class="px-6 py-3 text-sm text-gray-900 font-medium">{{ $promocao->novoCargoObj->nome ?? '—' }}</td>
                            <td class="px-6 py-3 text-sm text-green-700 font-medium">
                                {{ isset($promocao->novo_salario) ? 'R$ ' . number_format($promocao->novo_salario, 2, ',', '.') : '—' }}
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

</div>
@endsection
