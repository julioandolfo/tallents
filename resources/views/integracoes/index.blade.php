@extends('layouts.app')

@section('title', 'Integrações')
@section('page-title', 'Integrações')

@section('content')
<div class="space-y-5 py-4 max-w-3xl">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <p class="text-sm text-gray-500">Conecte serviços externos. As credenciais ficam guardadas no banco e podem ser preenchidas a qualquer momento.</p>

    @foreach($integracoes as $provider => $integracao)
        @php $meta = \App\Models\Integracao::PROVIDERS[$provider]; @endphp
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form method="POST" action="{{ route('integracoes.update', $provider) }}">
                @csrf @method('PUT')
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">{{ $meta['nome'] }}</h3>
                        <p class="text-sm text-gray-500">{{ $meta['desc'] }}</p>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-gray-700 shrink-0">
                        <input type="hidden" name="ativo" value="0">
                        <input type="checkbox" name="ativo" value="1" {{ $integracao->ativo ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                        Ativa
                    </label>
                </div>

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($meta['campos'] as $campo => $label)
                        <div class="{{ $loop->last && count($meta['campos']) % 2 ? 'sm:col-span-2' : '' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                            <input type="{{ str_contains($campo, 'token') || str_contains($campo, 'key') ? 'password' : 'text' }}"
                                   name="credenciais[{{ $campo }}]" value="{{ $integracao->valor($campo) }}" autocomplete="off"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 flex justify-end">
                    <button class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Salvar {{ $meta['nome'] }}</button>
                </div>
            </form>
        </div>
    @endforeach
</div>
@endsection
