@extends('layouts.app')

@section('title', $vaga->titulo)
@section('page-title', 'Vaga')

@section('content')
<div class="space-y-5 py-4">
    <div class="flex items-center justify-between gap-3">
        <a href="{{ route('vagas.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <a href="{{ route('vagas.edit', $vaga) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Editar vaga</a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    {{-- Cabeçalho da vaga --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $vaga->titulo }}</h2>
                <p class="text-sm text-gray-500">{{ optional($vaga->empresa)->nome ?? '—' }} · {{ $vaga->modalidadeLabel() }} · {{ $vaga->quantidade }} vaga(s)</p>
            </div>
            <x-ui.badge :color="$vaga->statusCor()">{{ $vaga->statusLabel() }}</x-ui.badge>
        </div>
        @if($vaga->salario_min || $vaga->salario_max)
            <p class="mt-2 text-sm text-gray-600">Faixa salarial: {{ $vaga->salario_min ? 'R$ ' . number_format($vaga->salario_min, 2, ',', '.') : '—' }} a {{ $vaga->salario_max ? 'R$ ' . number_format($vaga->salario_max, 2, ',', '.') : '—' }}</p>
        @endif
        @if($vaga->descricao)<div class="mt-3 text-sm text-gray-700 whitespace-pre-line">{{ $vaga->descricao }}</div>@endif
        @if($vaga->requisitos)<div class="mt-3"><p class="text-xs font-semibold text-gray-500 uppercase mb-1">Requisitos</p><div class="text-sm text-gray-700 whitespace-pre-line">{{ $vaga->requisitos }}</div></div>@endif
    </div>

    {{-- Adicionar candidato --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5" x-data="{ open: false }">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900">Candidatos</h3>
            <button @click="open = !open" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">+ Adicionar candidato</button>
        </div>
        <form x-show="open" x-cloak method="POST" action="{{ route('candidatos.store', $vaga) }}" enctype="multipart/form-data" class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @csrf
            <input type="text" name="nome" required placeholder="Nome *" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <input type="email" name="email" placeholder="E-mail" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <input type="text" name="telefone" placeholder="Telefone" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <input type="text" name="linkedin" placeholder="LinkedIn / portfólio" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <input type="number" name="nota" min="0" max="10" placeholder="Nota (0-10)" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <input type="file" name="curriculo" class="text-sm text-gray-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 file:text-sm">
            <div class="sm:col-span-2 lg:col-span-3 flex justify-end"><button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Adicionar</button></div>
        </form>
    </div>

    {{-- Funil / Kanban --}}
    <div class="overflow-x-auto pb-2">
        <div class="flex gap-4 min-w-max">
            @foreach(\App\Models\Candidato::ETAPAS as $etapa => $label)
                @php $lista = $candidatos[$etapa] ?? collect(); @endphp
                <div class="w-72 flex-shrink-0">
                    <div class="flex items-center justify-between px-1 mb-2">
                        <div class="flex items-center gap-2">
                            <x-ui.badge :color="\App\Models\Candidato::CORES[$etapa] ?? 'gray'">{{ $label }}</x-ui.badge>
                            <span class="text-xs text-gray-400">{{ $lista->count() }}</span>
                        </div>
                    </div>
                    <div class="space-y-2 bg-gray-50 rounded-xl p-2 min-h-24">
                        @forelse($lista as $cand)
                            <div class="bg-white rounded-lg border border-gray-200 p-3 shadow-sm">
                                <div class="flex items-start justify-between gap-2">
                                    <p class="text-sm font-medium text-gray-900">{{ $cand->nome }}</p>
                                    @if(!is_null($cand->nota))<span class="text-xs font-semibold text-indigo-600">{{ $cand->nota }}/10</span>@endif
                                </div>
                                @if($cand->email)<p class="text-xs text-gray-500 truncate">{{ $cand->email }}</p>@endif
                                <div class="mt-2 flex items-center gap-2 flex-wrap">
                                    @if($cand->curriculoUrl())<a href="{{ $cand->curriculoUrl() }}" target="_blank" class="text-xs text-indigo-600 hover:text-indigo-700">CV</a>@endif
                                    @if($cand->linkedin)<a href="{{ $cand->linkedin }}" target="_blank" class="text-xs text-indigo-600 hover:text-indigo-700">Perfil</a>@endif
                                </div>
                                <div class="mt-2 flex items-center gap-1">
                                    <form method="POST" action="{{ route('candidatos.mover', $cand) }}" class="flex-1">
                                        @csrf @method('PATCH')
                                        <select name="etapa" onchange="this.form.submit()" class="w-full text-xs px-2 py-1.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                            @foreach(\App\Models\Candidato::ETAPAS as $k => $v)
                                                <option value="{{ $k }}" {{ $cand->etapa === $k ? 'selected' : '' }}>{{ $v }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                    <form method="POST" action="{{ route('candidatos.destroy', $cand) }}" x-data @submit.prevent="if(confirm('Remover candidato?')) $el.submit()">
                                        @csrf @method('DELETE')
                                        <button class="p-1.5 text-gray-300 hover:text-red-600 rounded"><svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-300 text-center py-4">—</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
