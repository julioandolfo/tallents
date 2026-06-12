@extends('layouts.app')

@section('title', 'Nova Avaliação')
@section('page-title', 'Nova Avaliação')

@section('content')
<div class="py-4 max-w-3xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('avaliacoes.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h2 class="text-lg font-semibold text-gray-900">Nova Avaliação</h2>
    </div>
    <form method="POST" action="{{ route('avaliacoes.store') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        @include('avaliacoes._form')
    </form>
</div>
@endsection
