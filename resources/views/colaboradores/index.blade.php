@extends('layouts.app')

@section('title', 'Colaboradores')
@section('page-title', 'Colaboradores')

@section('content')
<div class="space-y-4 py-4">

    {{-- Cabeçalho com botão --}}
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $colaboradores->total() ?? 0 }} colaboradores encontrados</p>
        <a href="{{ route('colaboradores.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Novo Colaborador
        </a>
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('colaboradores.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Buscar</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text"
                           name="busca"
                           value="{{ request('busca') }}"
                           placeholder="Nome, CPF ou matrícula..."
                           class="block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>
            <div class="min-w-[160px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Empresa</label>
                <select name="empresa_id" class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Todas</option>
                    @foreach($empresas ?? [] as $empresa)
                        <option value="{{ $empresa->id }}" {{ request('empresa_id') == $empresa->id ? 'selected' : '' }}>
                            {{ $empresa->nome }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[140px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="status" class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Todos</option>
                    <option value="ATIVO" {{ request('status') == 'ATIVO' ? 'selected' : '' }}>Ativo</option>
                    <option value="INATIVO" {{ request('status') == 'INATIVO' ? 'selected' : '' }}>Inativo</option>
                    <option value="FERIAS" {{ request('status') == 'FERIAS' ? 'selected' : '' }}>Férias</option>
                    <option value="LICENCA" {{ request('status') == 'LICENCA' ? 'selected' : '' }}>Licença</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                    Filtrar
                </button>
                <a href="{{ route('colaboradores.index') }}"
                   class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">
                    Limpar
                </a>
            </div>
        </form>
    </div>

    {{-- Tabela --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-10">Foto</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Empresa</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Cargo</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Setor</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Admissão</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($colaboradores ?? [] as $colaborador)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-3">
                                @if($colaborador->foto)
                                    <img src="{{ Storage::url($colaborador->foto) }}"
                                         class="h-9 w-9 rounded-full object-cover">
                                @else
                                    <div class="h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <span class="text-indigo-700 text-xs font-semibold">{{ strtoupper(substr($colaborador->nome, 0, 2)) }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $colaborador->nome }}</p>
                                    <p class="text-xs text-gray-400">{{ $colaborador->cpf ?? $colaborador->matricula ?? '' }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-3 hidden md:table-cell">
                                <span class="text-sm text-gray-600">{{ $colaborador->empresa->nome ?? '—' }}</span>
                            </td>
                            <td class="px-6 py-3 hidden lg:table-cell">
                                <span class="text-sm text-gray-600">{{ $colaborador->cargo->nome ?? '—' }}</span>
                            </td>
                            <td class="px-6 py-3 hidden lg:table-cell">
                                <span class="text-sm text-gray-600">{{ $colaborador->setor->nome ?? '—' }}</span>
                            </td>
                            <td class="px-6 py-3">
                                @php
                                    $badges = [
                                        'ATIVO'    => 'bg-green-100 text-green-800',
                                        'INATIVO'  => 'bg-red-100 text-red-800',
                                        'FERIAS'   => 'bg-yellow-100 text-yellow-800',
                                        'LICENCA'  => 'bg-blue-100 text-blue-800',
                                    ];
                                    $badge = $badges[$colaborador->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                                    {{ $colaborador->status }}
                                </span>
                            </td>
                            <td class="px-6 py-3 hidden md:table-cell">
                                <span class="text-sm text-gray-600">
                                    {{ $colaborador->data_admissao ? \Carbon\Carbon::parse($colaborador->data_admissao)->format('d/m/Y') : '—' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('colaboradores.show', $colaborador) }}"
                                       class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded transition"
                                       title="Ver">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('colaboradores.edit', $colaborador) }}"
                                       class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded transition"
                                       title="Editar">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('colaboradores.destroy', $colaborador) }}"
                                          x-data
                                          @submit.prevent="if(confirm('Tem certeza que deseja excluir {{ addslashes($colaborador->nome) }}? Esta ação não pode ser desfeita.')) $el.submit()">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition"
                                                title="Excluir">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <svg class="h-12 w-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <p class="text-sm text-gray-500 font-medium">Nenhum colaborador encontrado</p>
                                <p class="text-xs text-gray-400 mt-1">Tente ajustar os filtros ou cadastre um novo colaborador</p>
                                <a href="{{ route('colaboradores.create') }}"
                                   class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Novo Colaborador
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        @if(isset($colaboradores) && $colaboradores->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $colaboradores->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
