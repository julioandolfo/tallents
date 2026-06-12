@extends('layouts.app')

@section('title', 'Editar Ocorrência')
@section('page-title', 'Editar Ocorrência')

@section('content')
<div class="py-4 max-w-2xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('ocorrencias.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h2 class="text-lg font-semibold text-gray-900">Editar Ocorrência</h2>
    </div>

    <form method="POST" action="{{ route('ocorrencias.update', $ocorrencia) }}"
          x-data="{ permiteAtraso: false, permitePonto: false,
                    sync(el){ const o = el.selectedOptions[0]; this.permiteAtraso = o && o.dataset.atraso === '1'; this.permitePonto = o && o.dataset.ponto === '1'; } }">
        @csrf
        @method('PUT')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Colaborador</label>
                <select name="colaborador_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @foreach($colaboradores ?? [] as $colaborador)
                        <option value="{{ $colaborador->id }}" {{ old('colaborador_id', $ocorrencia->colaborador_id) == $colaborador->id ? 'selected' : '' }}>
                            {{ $colaborador->nome }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Ocorrência</label>
                <select name="tipo_ocorrencia_id" required x-init="sync($el)" @change="sync($el)"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @foreach($tiposOcorrencias ?? [] as $tipo)
                        <option value="{{ $tipo->id }}" data-atraso="{{ $tipo->permite_tempo_atraso ? '1' : '0' }}" data-ponto="{{ $tipo->permite_tipo_ponto ? '1' : '0' }}"
                                {{ old('tipo_ocorrencia_id', $ocorrencia->tipo_ocorrencia_id) == $tipo->id ? 'selected' : '' }}>
                            {{ $tipo->nome }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data</label>
                    <input type="date" name="data" value="{{ old('data', optional($ocorrencia->data_ocorrencia)->format('Y-m-d')) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hora</label>
                    <input type="time" name="hora_ocorrencia" value="{{ old('hora_ocorrencia', $ocorrencia->hora_ocorrencia ? \Illuminate\Support\Str::substr($ocorrencia->hora_ocorrencia, 0, 5) : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div x-show="permiteAtraso" x-cloak>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tempo de atraso (minutos)</label>
                <input type="number" name="tempo_atraso_minutos" min="0" max="1440" value="{{ old('tempo_atraso_minutos', $ocorrencia->tempo_atraso_minutos) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div x-show="permitePonto" x-cloak>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de ponto</label>
                <select name="tipo_ponto" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Selecione</option>
                    @foreach(\App\Models\Ocorrencia::TIPOS_PONTO as $k => $v)
                        <option value="{{ $k }}" {{ old('tipo_ponto', $ocorrencia->tipo_ponto) == $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Observação</label>
                <textarea name="observacao" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('observacao', $ocorrencia->observacao) }}</textarea>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3 mt-4">
            <a href="{{ route('ocorrencias.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">Cancelar</a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Salvar Alterações</button>
        </div>
    </form>
</div>
@endsection
