@extends('layouts.app')

@section('title', 'Feedbacks')
@section('page-title', 'Feedbacks')

@section('content')
<div class="space-y-4 py-4">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $feedbacks->total() }} feedback(s)</p>
        <a href="{{ route('feedbacks.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Novo Feedback
        </a>
    </div>

    <div class="space-y-3">
        @forelse($feedbacks as $fb)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <x-ui.badge :color="$fb->cor()">{{ $fb->tipoLabel() }}</x-ui.badge>
                        @unless($fb->visivel_colaborador)<x-ui.badge color="gray">Interno</x-ui.badge>@endunless
                        <span class="text-sm font-medium text-gray-900">{{ optional($fb->colaborador)->nome ?? '—' }}</span>
                    </div>
                    <form method="POST" action="{{ route('feedbacks.destroy', $fb) }}" x-data @submit.prevent="if(confirm('Excluir feedback?')) $el.submit()">
                        @csrf @method('DELETE')
                        <button class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                    </form>
                </div>
                <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">{{ $fb->mensagem }}</p>
                <p class="mt-2 text-xs text-gray-400">{{ optional($fb->autor)->name ?? 'Sistema' }} · {{ optional($fb->data)->format('d/m/Y') }}</p>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-12 text-center text-sm text-gray-400">Nenhum feedback</div>
        @endforelse
    </div>

    @if($feedbacks->hasPages())
        <div>{{ $feedbacks->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
