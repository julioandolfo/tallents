@csrf
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Título <span class="text-red-500">*</span></label>
        <input type="text" name="titulo" value="{{ old('titulo', $documento->titulo ?? '') }}" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Categoria <span class="text-red-500">*</span></label>
        <select name="categoria" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @foreach(\App\Models\Documento::CATEGORIAS as $k => $v)
                <option value="{{ $k }}" {{ old('categoria', $documento->categoria ?? 'MANUAL') == $k ? 'selected' : '' }}>{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Empresa (opcional)</label>
        <select name="empresa_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Todas as empresas</option>
            @foreach($empresas as $e)
                <option value="{{ $e->id }}" {{ old('empresa_id', $documento->empresa_id ?? '') == $e->id ? 'selected' : '' }}>{{ $e->nome }}</option>
            @endforeach
        </select>
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
        <textarea name="descricao" rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('descricao', $documento->descricao ?? '') }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Arquivo (PDF, até 10MB)</label>
        <input type="file" name="arquivo" class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 file:text-sm file:font-medium hover:file:bg-indigo-100">
        @if(isset($documento) && $documento->arquivo)
            <p class="mt-1 text-xs text-gray-500">Arquivo atual mantido se nenhum novo for enviado.</p>
        @endif
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">…ou link externo</label>
        <input type="url" name="url" value="{{ old('url', $documento->url ?? '') }}" placeholder="https://…"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
</div>
<div class="flex justify-end gap-2 pt-4">
    <a href="{{ route('documentos.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">Cancelar</a>
    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">Salvar</button>
</div>
