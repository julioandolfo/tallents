@extends($layout)

@section('title', 'Chat — ' . optional($outro)->name)
@section('page-title', 'Chat Interno')

@section('content')
<div class="py-4">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col" style="height: 70vh;">
        {{-- Cabeçalho --}}
        <div class="px-5 py-3 border-b border-gray-200 flex items-center gap-3">
            <a href="{{ route('chat.index') }}" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <span class="h-9 w-9 rounded-full bg-indigo-600 flex items-center justify-center text-white text-sm font-semibold">{{ strtoupper(substr(optional($outro)->name ?? '?', 0, 1)) }}</span>
            <div>
                <p class="text-sm font-semibold text-gray-900">{{ optional($outro)->name ?? 'Usuário' }}</p>
                <p class="text-xs text-gray-400">{{ optional($outro)->email }}</p>
            </div>
        </div>

        {{-- Mensagens --}}
        <div class="flex-1 overflow-y-auto px-5 py-4 space-y-3 bg-gray-50" x-data x-init="$el.scrollTop = $el.scrollHeight">
            @forelse($conversa->mensagens as $msg)
                @php $minha = $msg->remetente_id === auth()->id(); @endphp
                <div class="flex {{ $minha ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs sm:max-w-md px-4 py-2 rounded-2xl text-sm {{ $minha ? 'bg-indigo-600 text-white rounded-br-sm' : 'bg-white border border-gray-200 text-gray-800 rounded-bl-sm' }}">
                        <p class="whitespace-pre-line">{{ $msg->corpo }}</p>
                        <p class="text-[10px] mt-1 {{ $minha ? 'text-indigo-200' : 'text-gray-400' }}">{{ $msg->created_at->format('d/m H:i') }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center text-sm text-gray-400 py-8">Envie a primeira mensagem</div>
            @endforelse
        </div>

        {{-- Envio --}}
        <form method="POST" action="{{ route('chat.enviar', $conversa) }}" class="px-4 py-3 border-t border-gray-200 flex items-center gap-2">
            @csrf
            <input type="text" name="corpo" required autocomplete="off" placeholder="Digite sua mensagem…" autofocus
                   class="flex-1 px-4 py-2.5 border border-gray-300 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <button class="h-10 w-10 flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white rounded-full transition">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            </button>
        </form>
    </div>
</div>
@endsection
