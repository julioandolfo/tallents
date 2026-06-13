@csrf
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Título da vaga <span class="text-red-500">*</span></label>
        <input type="text" name="titulo" value="{{ old('titulo', $vaga->titulo ?? '') }}" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Empresa</label>
        <select name="empresa_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">—</option>
            @foreach($empresas as $e)
                <option value="{{ $e->id }}" {{ old('empresa_id', $vaga->empresa_id ?? '') == $e->id ? 'selected' : '' }}>{{ $e->nome }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Cargo relacionado</label>
        <select name="cargo_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">—</option>
            @foreach($cargos as $c)
                <option value="{{ $c->id }}" {{ old('cargo_id', $vaga->cargo_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->nome }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Modalidade <span class="text-red-500">*</span></label>
        <select name="modalidade" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @foreach(\App\Models\Vaga::MODALIDADES as $k => $v)
                <option value="{{ $k }}" {{ old('modalidade', $vaga->modalidade ?? 'PRESENCIAL') == $k ? 'selected' : '' }}>{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de contrato</label>
        <input type="text" name="tipo_contrato" value="{{ old('tipo_contrato', $vaga->tipo_contrato ?? '') }}" placeholder="CLT, PJ, Estágio…"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Salário mín.</label>
        <input type="number" step="0.01" name="salario_min" data-money value="{{ old('salario_min', $vaga->salario_min ?? '') }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Salário máx.</label>
        <input type="number" step="0.01" name="salario_max" data-money value="{{ old('salario_max', $vaga->salario_max ?? '') }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Vagas <span class="text-red-500">*</span></label>
        <input type="number" name="quantidade" min="1" value="{{ old('quantidade', $vaga->quantidade ?? 1) }}" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
        <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @foreach(\App\Models\Vaga::STATUS as $k => $v)
                <option value="{{ $k }}" {{ old('status', $vaga->status ?? 'ABERTA') == $k ? 'selected' : '' }}>{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
        <textarea name="descricao" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('descricao', $vaga->descricao ?? '') }}</textarea>
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Requisitos</label>
        <textarea name="requisitos" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('requisitos', $vaga->requisitos ?? '') }}</textarea>
    </div>
</div>
<div class="flex justify-end gap-2 pt-4">
    <a href="{{ route('vagas.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">Cancelar</a>
    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Salvar</button>
</div>
