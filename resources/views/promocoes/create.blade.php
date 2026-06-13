@extends('layouts.app')

@section('title', 'Registrar Promoção')
@section('page-title', 'Registrar Promoção')

@section('content')
<div class="py-4 max-w-2xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('promocoes.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h2 class="text-lg font-semibold text-gray-900">Registrar Promoção</h2>
    </div>

    <form method="POST" action="{{ route('promocoes.store') }}">
        @csrf
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Colaborador <span class="text-red-500">*</span></label>
                <select name="colaborador_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Selecione o colaborador</option>
                    @foreach($colaboradores ?? [] as $colaborador)
                        <option value="{{ $colaborador->id }}"
                                data-salario="{{ $colaborador->salario }}" data-cargo="{{ $colaborador->cargo_id }}"
                                {{ (old('colaborador_id', request('colaborador_id'))) == $colaborador->id ? 'selected' : '' }}>
                            {{ $colaborador->nome }} — {{ $colaborador->cargo->nome ?? 'Sem cargo' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cargo Anterior</label>
                    <select name="cargo_anterior_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Selecione</option>
                        @foreach($cargos ?? [] as $cargo)
                            <option value="{{ $cargo->id }}" {{ old('cargo_anterior_id') == $cargo->id ? 'selected' : '' }}>{{ $cargo->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Novo Cargo <span class="text-red-500">*</span></label>
                    <select name="novo_cargo_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Selecione</option>
                        @foreach($cargos ?? [] as $cargo)
                            <option value="{{ $cargo->id }}" {{ old('novo_cargo_id') == $cargo->id ? 'selected' : '' }}>{{ $cargo->nome }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Salário Anterior (R$)</label>
                    <input type="number" name="salario_anterior" value="{{ old('salario_anterior') }}" step="0.01" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Novo Salário (R$)</label>
                    <input type="number" name="novo_salario" value="{{ old('novo_salario') }}" step="0.01" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data da Promoção <span class="text-red-500">*</span></label>
                <input type="date" name="data" value="{{ old('data', date('Y-m-d')) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Motivo / Observação</label>
                <textarea name="motivo" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('motivo') }}</textarea>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3 mt-4">
            <a href="{{ route('promocoes.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">Cancelar</a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Salvar Promoção</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const sel = document.querySelector('select[name="colaborador_id"]');
    if (!sel) return;
    function preencher() {
        const opt = sel.selectedOptions[0];
        if (!opt) return;
        const salAnt = document.querySelector('[name="salario_anterior"]');
        const cargoAnt = document.querySelector('[name="cargo_anterior_id"]');
        if (salAnt && opt.dataset.salario && !salAnt.value) salAnt.value = opt.dataset.salario;
        if (cargoAnt && opt.dataset.cargo) cargoAnt.value = opt.dataset.cargo;
    }
    sel.addEventListener('change', preencher);
    if (sel.value) preencher();
})();
</script>
@endpush
