@csrf
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Colaborador <span class="text-red-500">*</span></label>
        <select name="colaborador_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Selecione</option>
            @foreach($colaboradores as $c)
                <option value="{{ $c->id }}" {{ old('colaborador_id', $avaliacao->colaborador_id ?? ($colaboradorId ?? '')) == $c->id ? 'selected' : '' }}>{{ $c->nome }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Ciclo <span class="text-red-500">*</span></label>
        <input type="text" name="ciclo" value="{{ old('ciclo', $avaliacao->ciclo ?? date('Y') . '-S1') }}" required placeholder="2024-S1"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo <span class="text-red-500">*</span></label>
        <select name="tipo" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @foreach(\App\Models\Avaliacao::TIPOS as $k => $v)
                <option value="{{ $k }}" {{ old('tipo', $avaliacao->tipo ?? 'GESTOR') == $k ? 'selected' : '' }}>{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nota geral (0-10)</label>
        <input type="number" step="0.1" min="0" max="10" name="nota_geral" value="{{ old('nota_geral', $avaliacao->nota_geral ?? '') }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
        <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @foreach(\App\Models\Avaliacao::STATUS as $k => $v)
                <option value="{{ $k }}" {{ old('status', $avaliacao->status ?? 'PENDENTE') == $k ? 'selected' : '' }}>{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Data</label>
        <input type="date" name="data" value="{{ old('data', isset($avaliacao) && $avaliacao->data ? $avaliacao->data->format('Y-m-d') : date('Y-m-d')) }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Pontos fortes</label>
        <textarea name="pontos_fortes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('pontos_fortes', $avaliacao->pontos_fortes ?? '') }}</textarea>
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Pontos a melhorar</label>
        <textarea name="pontos_melhorar" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('pontos_melhorar', $avaliacao->pontos_melhorar ?? '') }}</textarea>
    </div>
</div>
<div class="flex justify-end gap-2 pt-4">
    <a href="{{ route('avaliacoes.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">Cancelar</a>
    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Salvar</button>
</div>
