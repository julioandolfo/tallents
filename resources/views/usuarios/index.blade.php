@extends('layouts.app')

@section('title', 'Usuários')
@section('page-title', 'Usuários do Sistema')

@section('content')
<div class="space-y-4 py-4">

    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $usuarios->total() ?? 0 }} usuários cadastrados</p>
        <a href="{{ route('usuarios.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Novo Usuário
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Usuário</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">E-mail</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Perfil</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Último acesso</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($usuarios ?? [] as $usuario)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                                    <span class="text-indigo-700 text-xs font-semibold">{{ strtoupper(substr($usuario->name, 0, 2)) }}</span>
                                </div>
                                <p class="text-sm font-medium text-gray-900">{{ $usuario->name }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell text-sm text-gray-600">{{ $usuario->email }}</td>
                        <td class="px-6 py-4">
                            @php
                                $perfilBadges = [
                                    'admin'       => 'bg-red-100 text-red-800',
                                    'rh'          => 'bg-purple-100 text-purple-800',
                                    'colaborador' => 'bg-green-100 text-green-800',
                                ];
                                $pb = $perfilBadges[$usuario->perfil ?? ''] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pb }}">
                                {{ ucfirst($usuario->perfil ?? 'colaborador') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 hidden lg:table-cell text-sm text-gray-600">
                            {{ $usuario->last_login_at ? \Carbon\Carbon::parse($usuario->last_login_at)->diffForHumans() : 'Nunca' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('usuarios.edit', $usuario) }}"
                                   class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded transition">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @if($usuario->id !== auth()->id())
                                <form method="POST" action="{{ route('usuarios.destroy', $usuario) }}"
                                      x-data @submit.prevent="if(confirm('Excluir usuário {{ addslashes($usuario->name) }}?')) $el.submit()">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-400">Nenhum usuário cadastrado</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if(isset($usuarios) && $usuarios->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">{{ $usuarios->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
