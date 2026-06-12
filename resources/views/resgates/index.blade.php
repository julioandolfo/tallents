@extends('layouts.app')

@section('title', 'Resgates')
@section('page-title', 'Resgates da Loja')

@section('content')
<div class="space-y-4 py-4">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-end gap-3">
        <div class="w-48">
            <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Todos</option>
                @foreach(\App\Models\Resgate::STATUS as $k => $v)<option value="{{ $k }}" {{ request('status') == $k ? 'selected' : '' }}>{{ $v }}</option>@endforeach
            </select>
        </div>
        <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Filtrar</button>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Colaborador</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Recompensa</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Custo</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($resgates as $r)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ optional($r->colaborador)->nome ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ optional($r->recompensa)->nome ?? '— (removida)' }}</td>
                        <td class="px-6 py-4 text-right text-sm font-semibold text-indigo-600">{{ $r->custo_pontos }} pts</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $r->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <form method="POST" action="{{ route('resgates.update', $r) }}" class="flex items-center gap-2">
                                @csrf @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="text-xs px-2 py-1.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    @foreach(\App\Models\Resgate::STATUS as $k => $v)<option value="{{ $k }}" {{ $r->status === $k ? 'selected' : '' }}>{{ $v }}</option>@endforeach
                                </select>
                                <x-ui.badge :color="$r->cor()">{{ $r->statusLabel() }}</x-ui.badge>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-12 text-center text-sm text-gray-400">Nenhum resgate</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($resgates->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">{{ $resgates->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
