@csrf
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Título <span class="text-red-500">*</span></label>
        <input type="text" name="titulo" value="{{ old('titulo', $evento->titulo ?? '') }}" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo <span class="text-red-500">*</span></label>
        <select name="tipo" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @foreach(\App\Models\Evento::TIPOS as $k => $v)
                <option value="{{ $k }}" {{ old('tipo', $evento->tipo ?? 'REUNIAO') == $k ? 'selected' : '' }}>{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Empresa (opcional)</label>
        <select name="empresa_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Todas as empresas</option>
            @foreach($empresas as $e)
                <option value="{{ $e->id }}" {{ old('empresa_id', $evento->empresa_id ?? '') == $e->id ? 'selected' : '' }}>{{ $e->nome }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Início <span class="text-red-500">*</span></label>
        <input type="datetime-local" name="inicio" required
               value="{{ old('inicio', isset($evento) && $evento->inicio ? $evento->inicio->format('Y-m-d\TH:i') : '') }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Fim</label>
        <input type="datetime-local" name="fim"
               value="{{ old('fim', isset($evento) && $evento->fim ? $evento->fim->format('Y-m-d\TH:i') : '') }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Local</label>
        <input type="text" name="local" value="{{ old('local', $evento->local ?? '') }}" placeholder="Sala, link da reunião, endereço…"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
        <textarea name="descricao" rows="4"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('descricao', $evento->descricao ?? '') }}</textarea>
    </div>
</div>
<div class="flex justify-end gap-2 pt-4">
    <a href="{{ route('eventos.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">Cancelar</a>
    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Salvar</button>
</div>
