@extends($layout)

@section('title', 'Chat')
@section('page-title', 'Chat Interno')

@section('content')
<div class="py-4" x-data="{ novo: false }">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Conversas</h2>
        <button @click="novo = !novo" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nova conversa
        </button>
    </div>

    <div x-show="novo" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-4">
        <p class="text-sm font-medium text-gray-700 mb-3">Iniciar conversa com:</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 max-h-64 overflow-y-auto">
            @forelse($usuarios as $u)
                <form method="POST" action="{{ route('chat.iniciar', $u) }}">
                    @csrf
                    <button class="w-full flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-50 border border-gray-100 text-left transition">
                        <span class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xs font-semibold">{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                        <span class="text-sm text-gray-700 truncate">{{ $u->name }}</span>
                    </button>
                </form>
            @empty
                <p class="text-sm text-gray-400">Nenhum usuário disponível</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="divide-y divide-gray-100">
            @forelse($conversas as $c)
                @php $outro = $c->outro(auth()->id()); @endphp
                <a href="{{ route('chat.show', $c) }}" class="flex items-center gap-3 px-6 py-4 hover:bg-gray-50 transition">
                    <span class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white text-sm font-semibold shrink-0">{{ strtoupper(substr(optional($outro)->name ?? '?', 0, 1)) }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">{{ optional($outro)->name ?? 'Usuário' }}</p>
                        <p class="text-xs text-gray-400">{{ optional($c->ultima_mensagem_em)->diffForHumans() ?? 'Sem mensagens' }}</p>
                    </div>
                </a>
            @empty
                <div class="px-6 py-12 text-center text-sm text-gray-400">Nenhuma conversa ainda. Inicie uma nova!</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
