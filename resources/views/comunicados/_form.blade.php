@csrf
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Título <span class="text-red-500">*</span></label>
        <input type="text" name="titulo" value="{{ old('titulo', $comunicado->titulo ?? '') }}" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Categoria <span class="text-red-500">*</span></label>
        <select name="categoria" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @foreach(\App\Models\Comunicado::CATEGORIAS as $k => $v)
                <option value="{{ $k }}" {{ old('categoria', $comunicado->categoria ?? 'GERAL') == $k ? 'selected' : '' }}>{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Empresa (opcional)</label>
        <select name="empresa_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Todas as empresas</option>
            @foreach($empresas as $e)
                <option value="{{ $e->id }}" {{ old('empresa_id', $comunicado->empresa_id ?? '') == $e->id ? 'selected' : '' }}>{{ $e->nome }}</option>
            @endforeach
        </select>
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Conteúdo <span class="text-red-500">*</span></label>
        <textarea name="conteudo" rows="8" required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('conteudo', $comunicado->conteudo ?? '') }}</textarea>
    </div>
    <label class="flex items-center gap-2 text-sm text-gray-700">
        <input type="hidden" name="destaque" value="0">
        <input type="checkbox" name="destaque" value="1" {{ old('destaque', $comunicado->destaque ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
        Fixar em destaque
    </label>
    <label class="flex items-center gap-2 text-sm text-gray-700">
        <input type="hidden" name="publicado" value="0">
        <input type="checkbox" name="publicado" value="1" {{ old('publicado', $comunicado->publicado ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
        Publicar agora (visível no mural)
    </label>
</div>
<div class="flex justify-end gap-2 pt-4">
    <a href="{{ route('comunicados.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">Cancelar</a>
    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Salvar</button>
</div>
