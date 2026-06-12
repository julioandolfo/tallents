@extends('layouts.app')

@section('title', $quadro->nome)
@section('page-title', $quadro->nome)

@section('content')
<div class="space-y-5 py-4">
    <div class="flex items-center justify-between gap-3">
        <a href="{{ route('quadros.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <form method="POST" action="{{ route('quadros.destroy', $quadro) }}" x-data @submit.prevent="if(confirm('Excluir quadro e todas as tarefas?')) $el.submit()">
            @csrf @method('DELETE')
            <button class="px-4 py-2 text-sm text-red-600 hover:text-red-700 font-medium">Excluir quadro</button>
        </form>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    {{-- Adicionar tarefa --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5" x-data="{ open: false }">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900">Tarefas</h3>
            <button @click="open = !open" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">+ Nova tarefa</button>
        </div>
        <form x-show="open" x-cloak method="POST" action="{{ route('tarefas.store', $quadro) }}" class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @csrf
            <input type="text" name="titulo" required placeholder="Título *" class="lg:col-span-2 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <select name="prioridade" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @foreach(\App\Models\Tarefa::PRIORIDADES as $k => $v)<option value="{{ $k }}" {{ $k === 'MEDIA' ? 'selected' : '' }}>Prioridade: {{ $v }}</option>@endforeach
            </select>
            <select name="responsavel_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Sem responsável</option>
                @foreach($usuarios as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach
            </select>
            <input type="text" name="descricao" placeholder="Descrição" class="lg:col-span-2 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <input type="date" name="prazo" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Adicionar</button>
        </form>
    </div>

    {{-- Kanban --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach(\App\Models\Tarefa::STATUS as $status => $label)
            @php $lista = $tarefas[$status] ?? collect(); @endphp
            <div class="bg-gray-50 rounded-xl p-3">
                <div class="flex items-center justify-between px-1 mb-3">
                    <h4 class="text-sm font-semibold text-gray-700">{{ $label }}</h4>
                    <span class="text-xs text-gray-400">{{ $lista->count() }}</span>
                </div>
                <div class="space-y-2 min-h-24">
                    @forelse($lista as $tarefa)
                        <div class="bg-white rounded-lg border border-gray-200 p-3 shadow-sm">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-medium text-gray-900">{{ $tarefa->titulo }}</p>
                                <x-ui.badge :color="$tarefa->corPrioridade()">{{ $tarefa->prioridadeLabel() }}</x-ui.badge>
                            </div>
                            @if($tarefa->descricao)<p class="text-xs text-gray-500 mt-1">{{ \Illuminate\Support\Str::limit($tarefa->descricao, 100) }}</p>@endif
                            <div class="mt-2 flex items-center justify-between text-xs text-gray-400">
                                <span>{{ optional($tarefa->responsavel)->name ?? 'Sem responsável' }}</span>
                                @if($tarefa->prazo)<span>{{ $tarefa->prazo->format('d/m') }}</span>@endif
                            </div>
                            <div class="mt-2 flex items-center gap-1">
                                <form method="POST" action="{{ route('tarefas.mover', $tarefa) }}" class="flex-1">
                                    @csrf @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="w-full text-xs px-2 py-1.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        @foreach(\App\Models\Tarefa::STATUS as $k => $v)<option value="{{ $k }}" {{ $tarefa->status === $k ? 'selected' : '' }}>{{ $v }}</option>@endforeach
                                    </select>
                                </form>
                                <form method="POST" action="{{ route('tarefas.destroy', $tarefa) }}" x-data @submit.prevent="if(confirm('Remover tarefa?')) $el.submit()">
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
@endsection
