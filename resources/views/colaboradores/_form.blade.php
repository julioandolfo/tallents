@php $c = $colaborador ?? null; @endphp

<div x-data="{
    activeTab: 'pessoal',
    fotoPreview: null,
    setFoto(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => { this.fotoPreview = e.target.result; };
            reader.readAsDataURL(file);
        }
    }
}">
    {{-- Abas --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4">
        <div class="flex overflow-x-auto border-b border-gray-200">
            @foreach([
                ['key' => 'pessoal',   'label' => 'Dados Pessoais'],
                ['key' => 'contrato',  'label' => 'Contrato & Cargo'],
                ['key' => 'endereco',  'label' => 'Endereço'],
                ['key' => 'bancario',  'label' => 'Dados Bancários'],
                ['key' => 'observacoes', 'label' => 'Observações'],
                ['key' => 'acesso',    'label' => 'Acesso ao Sistema'],
            ] as $tab)
            <button type="button"
                    @click="activeTab = '{{ $tab['key'] }}'"
                    :class="activeTab === '{{ $tab['key'] }}' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium border-b-2 transition whitespace-nowrap">
                {{ $tab['label'] }}
            </button>
            @endforeach
        </div>

        {{-- Aba: Dados Pessoais --}}
        <div x-show="activeTab === 'pessoal'" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1 flex flex-col items-center">
                    <div class="h-32 w-32 rounded-full overflow-hidden bg-gray-100 border-2 border-gray-200 mb-3">
                        <template x-if="fotoPreview"><img :src="fotoPreview" class="h-full w-full object-cover"></template>
                        <template x-if="!fotoPreview">
                            @if($c && $c->foto)
                                <img src="{{ Storage::url($c->foto) }}" class="h-full w-full object-cover">
                            @else
                                <div class="h-full w-full flex flex-col items-center justify-center text-gray-400">
                                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                            @endif
                        </template>
                    </div>
                    <label class="cursor-pointer px-4 py-2 text-xs font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-lg transition">
                        <input type="file" name="foto" accept="image/*" class="hidden" @change="setFoto($event)">
                        {{ $c && $c->foto ? 'Trocar Foto' : 'Selecionar Foto' }}
                    </label>
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG — Máx. 2MB</p>
                </div>

                <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo <span class="text-red-500">*</span></label>
                        <input type="text" name="nome" value="{{ old('nome', $c->nome ?? '') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CPF</label>
                        <input type="text" name="cpf" value="{{ old('cpf', $c->cpf ?? '') }}" placeholder="000.000.000-00"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">RG</label>
                        <input type="text" name="rg" value="{{ old('rg', $c->rg ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data de Nascimento</label>
                        <input type="date" name="data_nascimento" value="{{ old('data_nascimento', optional($c?->data_nascimento)->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sexo</label>
                        <select name="sexo" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Selecione</option>
                            @foreach(['M' => 'Masculino', 'F' => 'Feminino', 'O' => 'Outro'] as $k => $v)
                                <option value="{{ $k }}" {{ old('sexo', $c->sexo ?? '') == $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado Civil</label>
                        <select name="estado_civil" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Selecione</option>
                            @foreach(['solteiro' => 'Solteiro(a)', 'casado' => 'Casado(a)', 'divorciado' => 'Divorciado(a)', 'viuvo' => 'Viúvo(a)'] as $k => $v)
                                <option value="{{ $k }}" {{ old('estado_civil', $c->estado_civil ?? '') == $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                        <input type="text" name="telefone" value="{{ old('telefone', $c->telefone ?? '') }}" placeholder="(00) 0000-0000"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Celular</label>
                        <input type="text" name="celular" value="{{ old('celular', $c->celular ?? '') }}" placeholder="(00) 90000-0000"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">E-mail Pessoal</label>
                        <input type="email" name="email_pessoal" value="{{ old('email_pessoal', $c->email_pessoal ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PIS/PASEP</label>
                        <input type="text" name="pis" value="{{ old('pis', $c->pis ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CTPS</label>
                        <input type="text" name="ctps" value="{{ old('ctps', $c->ctps ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>
        </div>

        {{-- Aba: Contrato & Cargo --}}
        <div x-show="activeTab === 'contrato'" x-cloak class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Empresa <span class="text-red-500">*</span></label>
                    <select name="empresa_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Selecione a empresa</option>
                        @foreach($empresas ?? [] as $empresa)
                            <option value="{{ $empresa->id }}" {{ old('empresa_id', $c->empresa_id ?? '') == $empresa->id ? 'selected' : '' }}>{{ $empresa->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cargo</label>
                    <select name="cargo_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Selecione o cargo</option>
                        @foreach($cargos ?? [] as $cargo)
                            <option value="{{ $cargo->id }}" {{ old('cargo_id', $c->cargo_id ?? '') == $cargo->id ? 'selected' : '' }}>{{ $cargo->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Setor</label>
                    <select name="setor_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Selecione o setor</option>
                        @foreach($setores ?? [] as $setor)
                            <option value="{{ $setor->id }}" {{ old('setor_id', $c->setor_id ?? '') == $setor->id ? 'selected' : '' }}>{{ $setor->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nível Hierárquico</label>
                    <select name="nivel_hierarquico_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Selecione</option>
                        @foreach($niveisHierarquicos ?? [] as $nivel)
                            <option value="{{ $nivel->id }}" {{ old('nivel_hierarquico_id', $c->nivel_hierarquico_id ?? '') == $nivel->id ? 'selected' : '' }}>{{ $nivel->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Líder / Gestor direto</label>
                    <select name="lider_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Selecione</option>
                        @foreach($lideres ?? [] as $lider)
                            <option value="{{ $lider->id }}" {{ old('lider_id', $c->lider_id ?? '') == $lider->id ? 'selected' : '' }}>{{ $lider->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data de Admissão</label>
                    <input type="date" name="data_admissao" value="{{ old('data_admissao', optional($c?->data_admissao)->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Matrícula</label>
                    <input type="text" name="matricula" value="{{ old('matricula', $c->matricula ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Salário Base (R$)</label>
                    <input type="number" name="salario" value="{{ old('salario', $c->salario ?? '') }}" step="0.01" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Regime de Trabalho</label>
                    <select name="regime_trabalho" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Selecione</option>
                        @foreach(['CLT' => 'CLT', 'PJ' => 'PJ', 'ESTAGIO' => 'Estágio', 'AUTONOMO' => 'Autônomo'] as $k => $v)
                            <option value="{{ $k }}" {{ old('regime_trabalho', $c->tipo_contrato ?? '') == $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach(['ATIVO' => 'Ativo', 'INATIVO' => 'Inativo', 'FERIAS' => 'Férias', 'LICENCA' => 'Licença'] as $k => $v)
                            <option value="{{ $k }}" {{ old('status', $c->status ?? 'ATIVO') == $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Carga Horária Semanal</label>
                    <input type="number" name="carga_horaria" value="{{ old('carga_horaria', $c->carga_horaria ?? 44) }}" min="0" max="100"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        {{-- Aba: Endereço --}}
        <div x-show="activeTab === 'endereco'" x-cloak class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                    <input type="text" name="cep" value="{{ old('cep', $c->cep ?? '') }}" placeholder="00000-000"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logradouro</label>
                    <input type="text" name="logradouro" value="{{ old('logradouro', $c->logradouro ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                    <input type="text" name="numero" value="{{ old('numero', $c->numero ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                    <input type="text" name="complemento" value="{{ old('complemento', $c->complemento ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
                    <input type="text" name="bairro" value="{{ old('bairro', $c->bairro ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                    <input type="text" name="cidade" value="{{ old('cidade', $c->cidade ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select name="estado" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Selecione</option>
                        @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                            <option value="{{ $uf }}" {{ old('estado', $c->estado ?? '') == $uf ? 'selected' : '' }}>{{ $uf }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Aba: Dados Bancários --}}
        <div x-show="activeTab === 'bancario'" x-cloak class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Banco</label>
                    <input type="text" name="banco" value="{{ old('banco', $c->banco ?? '') }}" placeholder="Ex: 341 - Itaú"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Conta</label>
                    <select name="tipo_conta" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Selecione</option>
                        @foreach(['CORRENTE' => 'Conta Corrente', 'POUPANCA' => 'Conta Poupança'] as $k => $v)
                            <option value="{{ $k }}" {{ old('tipo_conta', $c->tipo_conta ?? '') == $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Agência</label>
                    <input type="text" name="agencia" value="{{ old('agencia', $c->agencia ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Conta</label>
                    <input type="text" name="conta" value="{{ old('conta', $c->conta ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chave PIX</label>
                    <input type="text" name="pix" value="{{ old('pix', $c->pix ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CNPJ (prestador PJ)</label>
                    <input type="text" name="cnpj" value="{{ old('cnpj', $c->cnpj ?? '') }}" placeholder="00.000.000/0000-00"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        {{-- Aba: Observações --}}
        <div x-show="activeTab === 'observacoes'" x-cloak class="p-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Observações gerais</label>
            <textarea name="observacoes" rows="8" placeholder="Anotações internas sobre o colaborador…"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('observacoes', $c->observacoes ?? '') }}</textarea>
        </div>

        {{-- Aba: Acesso ao Sistema --}}
        <div x-show="activeTab === 'acesso'" x-cloak class="p-6">
            <div class="max-w-md space-y-4">
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-700">
                        {{ $c ? 'Atualize o acesso ao sistema. Deixe a senha em branco para mantê-la.' : 'Preencha para criar um acesso ao sistema. Deixe em branco se o colaborador não precisar de acesso.' }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-mail para Login</label>
                    <input type="email" name="email_login" value="{{ old('email_login', $c->email_login ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                    <input type="password" name="password" autocomplete="new-password"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Senha</label>
                    <input type="password" name="password_confirmation" autocomplete="new-password"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Perfil de Acesso</label>
                    <select name="perfil" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Sem acesso</option>
                        @php $perfilAtual = old('perfil', $c && $c->user ? strtolower($c->user->role) : ''); @endphp
                        @foreach(['colaborador' => 'Colaborador', 'gestor' => 'Gestor', 'rh' => 'RH', 'admin' => 'Administrador'] as $k => $v)
                            <option value="{{ $k }}" {{ $perfilAtual == $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Ações --}}
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('colaboradores.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">Cancelar</a>
        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">{{ $c ? 'Salvar Alterações' : 'Salvar Colaborador' }}</button>
    </div>
</div>
