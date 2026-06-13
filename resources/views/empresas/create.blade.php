@extends('layouts.app')

@section('title', 'Nova Empresa')
@section('page-title', 'Nova Empresa')

@section('content')
<div class="py-4 max-w-3xl">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('empresas.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h2 class="text-lg font-semibold text-gray-900">Cadastrar Nova Empresa</h2>
    </div>

    <form method="POST" action="{{ route('empresas.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">

            <div>
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Dados da Empresa</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome Fantasia <span class="text-red-500">*</span></label>
                        <input type="text" name="nome" value="{{ old('nome') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Razão Social</label>
                        <input type="text" name="razao_social" value="{{ old('razao_social') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CNPJ</label>
                        <div class="flex gap-2">
                            <input type="text" name="cnpj" value="{{ old('cnpj') }}" placeholder="00.000.000/0000-00"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <button type="button" data-cnpj-buscar class="px-3 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition whitespace-nowrap">Buscar</button>
                        </div>
                        <p data-cnpj-msg class="text-xs text-gray-400 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                        <input type="text" name="telefone" value="{{ old('telefone') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Site</label>
                        <input type="url" name="site" value="{{ old('site') }}" placeholder="https://"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-4">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Endereço</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                        <input type="text" name="cep" value="{{ old('cep') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Logradouro</label>
                        <input type="text" name="logradouro" value="{{ old('logradouro') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                        <input type="text" name="numero" value="{{ old('numero') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                        <input type="text" name="complemento" value="{{ old('complemento') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
                        <input type="text" name="bairro" value="{{ old('bairro') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                        <input type="text" name="cidade" value="{{ old('cidade') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select name="estado" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Selecione</option>
                            @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                                <option value="{{ $uf }}" {{ old('estado') == $uf ? 'selected' : '' }}>{{ $uf }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-4">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Configurações</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Percentual de Hora Extra (%)</label>
                        <input type="number" step="0.01" min="0" name="percentual_hora_extra" value="{{ old('percentual_hora_extra', 50) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <p class="text-xs text-gray-400 mt-1">Adicional aplicado no cálculo de horas extras.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                        <input type="file" name="logo" accept="image/*" class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 file:text-sm file:font-medium">
                    </div>
                    <label class="flex items-center gap-2 text-sm text-gray-700 sm:col-span-2">
                        <input type="hidden" name="ativa" value="0">
                        <input type="checkbox" name="ativa" value="1" {{ old('ativa', true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                        Empresa ativa
                    </label>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 mt-4">
            <a href="{{ route('empresas.index') }}"
               class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">
                Cancelar
            </a>
            <button type="submit"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                Salvar Empresa
            </button>
        </div>
    </form>
</div>
@endsection

@push("scripts")
<script>
(function () {
    const btn = document.querySelector("[data-cnpj-buscar]");
    if (!btn) return;
    const campo = document.querySelector("input[name=\"cnpj\"]");
    const msg = document.querySelector("[data-cnpj-msg]");
    const set = (name, val) => { const el = document.querySelector(`[name="${name}"]`); if (el && val) el.value = val; };
    btn.addEventListener("click", async () => {
        const cnpj = (campo.value || "").replace(/\D/g, "");
        if (cnpj.length !== 14) { msg.textContent = "Informe 14 dígitos."; msg.className = "text-xs text-red-500 mt-1"; return; }
        msg.textContent = "Consultando..."; msg.className = "text-xs text-gray-400 mt-1";
        try {
            const r = await fetch("/api/cnpj/" + cnpj, { headers: { "Accept": "application/json" }, credentials: "same-origin" });
            const d = await r.json();
            if (!r.ok) { msg.textContent = d.error || "Não encontrado."; msg.className = "text-xs text-red-500 mt-1"; return; }
            set("nome", d.fantasia || d.nome);
            set("razao_social", d.nome);
            set("email", d.email);
            set("telefone", d.telefone);
            set("cep", d.cep);
            set("logradouro", d.logradouro);
            set("numero", d.numero);
            set("complemento", d.complemento);
            set("bairro", d.bairro);
            set("cidade", d.municipio);
            const uf = document.querySelector("[name=\"estado\"]"); if (uf && d.uf) uf.value = d.uf;
            msg.textContent = "Dados preenchidos a partir do CNPJ."; msg.className = "text-xs text-emerald-600 mt-1";
        } catch (e) { msg.textContent = "Falha na consulta."; msg.className = "text-xs text-red-500 mt-1"; }
    });
})();
</script>
@endpush
