@extends('layouts.app')

@section('title', 'Novo Usuário')
@section('page-title', 'Novo Usuário')

@section('content')
<div class="py-4 max-w-xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('usuarios.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h2 class="text-lg font-semibold text-gray-900">Cadastrar Novo Usuário</h2>
    </div>

    <form method="POST" action="{{ route('usuarios.store') }}">
        @csrf
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">E-mail <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Perfil de Acesso <span class="text-red-500">*</span></label>
                <select name="perfil" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Selecione</option>
                    <option value="colaborador" {{ old('perfil') == 'colaborador' ? 'selected' : '' }}>Colaborador</option>
                    <option value="rh" {{ old('perfil') == 'rh' ? 'selected' : '' }}>RH</option>
                    <option value="admin" {{ old('perfil') == 'admin' ? 'selected' : '' }}>Administrador</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Senha <span class="text-red-500">*</span></label>
                <input type="password" name="password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Senha <span class="text-red-500">*</span></label>
                <input type="password" name="password_confirmation" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>
        <div class="flex items-center justify-end gap-3 mt-4">
            <a href="{{ route('usuarios.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">Cancelar</a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Criar Usuário</button>
        </div>
    </form>
</div>
@endsection
