@php $u = $usuario ?? null; $empresasVinculadas = $u ? $u->empresas->pluck('id')->all() : []; @endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $u->name ?? '') }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">E-mail <span class="text-red-500">*</span></label>
            <input type="email" name="email" value="{{ old('email', $u->email ?? '') }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">CPF</label>
            <input type="text" name="cpf" value="{{ old('cpf', $u->cpf ?? '') }}" placeholder="000.000.000-00"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Perfil de Acesso <span class="text-red-500">*</span></label>
            <select name="perfil" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Selecione</option>
                @php $perfilAtual = old('perfil', $u ? strtolower($u->role) : ''); @endphp
                @foreach(['colaborador' => 'Colaborador', 'gestor' => 'Gestor', 'rh' => 'RH', 'admin' => 'Administrador'] as $k => $v)
                    <option value="{{ $k }}" {{ $perfilAtual == $k ? 'selected' : '' }}>{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Colaborador vinculado</label>
            <select name="colaborador_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Nenhum</option>
                @foreach($colaboradores ?? [] as $colab)
                    <option value="{{ $colab->id }}" {{ old('colaborador_id', $u->colaborador_id ?? '') == $colab->id ? 'selected' : '' }}>{{ $colab->nome }}</option>
                @endforeach
            </select>
            <p class="text-xs text-gray-400 mt-1">Liga este login à ficha de um colaborador (portal).</p>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Empresas com acesso</label>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mt-1">
            @foreach($empresas ?? [] as $empresa)
                <label class="flex items-center gap-2 text-sm text-gray-700 px-3 py-2 border border-gray-200 rounded-lg">
                    <input type="checkbox" name="empresa_ids[]" value="{{ $empresa->id }}"
                           {{ in_array($empresa->id, old('empresa_ids', $empresasVinculadas)) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-indigo-600">
                    {{ $empresa->nome }}
                </label>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Senha {{ $u ? '' : '*' }}</label>
            <input type="password" name="password" autocomplete="new-password" {{ $u ? '' : 'required' }}
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @if($u)<p class="text-xs text-gray-400 mt-1">Deixe em branco para manter a atual.</p>@endif
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Senha {{ $u ? '' : '*' }}</label>
            <input type="password" name="password_confirmation" autocomplete="new-password" {{ $u ? '' : 'required' }}
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
    </div>

    <label class="flex items-center gap-2 text-sm text-gray-700">
        <input type="hidden" name="ativo" value="0">
        <input type="checkbox" name="ativo" value="1" {{ old('ativo', $u->ativo ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
        Usuário ativo (pode fazer login)
    </label>
</div>
