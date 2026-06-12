@extends('layouts.app')

@section('title', 'Novo Feedback')
@section('page-title', 'Novo Feedback')

@section('content')
<div class="py-4 max-w-2xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('feedbacks.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h2 class="text-lg font-semibold text-gray-900">Registrar Feedback</h2>
    </div>
    <form method="POST" action="{{ route('feedbacks.store') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Colaborador <span class="text-red-500">*</span></label>
            <select name="colaborador_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Selecione</option>
                @foreach($colaboradores as $c)
                    <option value="{{ $c->id }}" {{ old('colaborador_id', $colaboradorId) == $c->id ? 'selected' : '' }}>{{ $c->nome }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo <span class="text-red-500">*</span></label>
            <select name="tipo" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @foreach(\App\Models\Feedback::TIPOS as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Mensagem <span class="text-red-500">*</span></label>
            <textarea name="mensagem" rows="5" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('mensagem') }}</textarea>
        </div>
        <label class="flex items-center gap-2 text-sm text-gray-700">
            <input type="hidden" name="visivel_colaborador" value="0">
            <input type="checkbox" name="visivel_colaborador" value="1" checked class="rounded border-gray-300 text-indigo-600">
            Visível para o colaborador no portal
        </label>
        <div class="flex justify-end gap-2 pt-2">
            <a href="{{ route('feedbacks.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">Cancelar</a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Registrar</button>
        </div>
    </form>
</div>
@endsection
