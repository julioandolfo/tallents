@extends('layouts.app')

@section('title', 'Organograma')
@section('page-title', 'Organograma')

@section('content')
<div class="space-y-4 py-4">

    <form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex flex-wrap items-end gap-3">
        <div class="w-64">
            <label class="block text-xs font-medium text-gray-600 mb-1">Empresa</label>
            <select name="empresa_id" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @foreach($empresas as $e)
                    <option value="{{ $e->id }}" {{ $empresaId == $e->id ? 'selected' : '' }}>{{ $e->nome }}</option>
                @endforeach
            </select>
        </div>
        <p class="text-sm text-gray-500 ml-auto">{{ $colaboradores->count() }} colaborador(es) ativos · hierarquia por líder</p>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 overflow-x-auto">
        @if($raizes->isEmpty())
            <p class="text-sm text-gray-400 text-center py-12">Nenhum colaborador para exibir. Defina o <strong>líder/gestor</strong> na ficha dos colaboradores para montar a hierarquia.</p>
        @else
            <ul class="space-y-4">
                @foreach($raizes as $raiz)
                    @include('organograma.no', ['colaborador' => $raiz, 'porLider' => $porLider])
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
