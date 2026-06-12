@extends('layouts.app')

@section('title', 'Editar Colaborador')
@section('page-title', 'Editar Colaborador')

@section('content')
<div class="py-4">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('colaboradores.show', $colaborador) }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Editar {{ $colaborador->nome }}</h2>
            <p class="text-sm text-gray-500">Atualize os dados do colaborador</p>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-4">
            <p class="font-medium">Corrija os campos abaixo:</p>
            <ul class="list-disc list-inside mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('colaboradores.update', $colaborador) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('colaboradores._form')
    </form>
</div>
@endsection
