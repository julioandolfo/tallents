@csrf
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Nome <span class="text-red-500">*</span></label>
        <input type="text" name="nome" value="{{ old('nome', $recompensa->nome ?? '') }}" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Custo (pontos) <span class="text-red-500">*</span></label>
        <input type="number" name="custo_pontos" min="1" value="{{ old('custo_pontos', $recompensa->custo_pontos ?? '') }}" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Estoque (vazio = ilimitado)</label>
        <input type="number" name="estoque" min="0" value="{{ old('estoque', $recompensa->estoque ?? '') }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
        <textarea name="descricao" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('descricao', $recompensa->descricao ?? '') }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Imagem</label>
        <input type="file" name="imagem" accept="image/*" class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 file:text-sm file:font-medium">
    </div>
    <label class="flex items-center gap-2 text-sm text-gray-700 self-end">
        <input type="hidden" name="ativa" value="0">
        <input type="checkbox" name="ativa" value="1" {{ old('ativa', $recompensa->ativa ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
        Ativa na loja
    </label>
</div>
<div class="flex justify-end gap-2 pt-4">
    <a href="{{ route('recompensas.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">Cancelar</a>
    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Salvar</button>
</div>
