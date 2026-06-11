@extends('layouts.app')

@section('title', 'Configurações')
@section('page-title', 'Configurações do Sistema')

@section('content')
<div class="py-4" x-data="{ activeTab: 'email' }">

    <div class="flex gap-6">

        <!-- Menu lateral de abas -->
        <div class="w-52 shrink-0">
            <nav class="space-y-1">
                @foreach([
                    ['key' => 'email',    'label' => 'E-mail / SMTP',   'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                    ['key' => 'empresa',  'label' => 'Dados da Empresa', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5'],
                    ['key' => 'sistema',  'label' => 'Sistema',          'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                ] as $tab)
                <button type="button"
                        @click="activeTab = '{{ $tab['key'] }}'"
                        :class="activeTab === '{{ $tab['key'] }}' ? 'bg-indigo-50 text-indigo-700 border-indigo-500' : 'text-gray-600 border-transparent hover:bg-gray-50'"
                        class="w-full flex items-center gap-2 px-3 py-2.5 text-sm font-medium rounded-lg border-l-2 transition text-left">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/>
                    </svg>
                    {{ $tab['label'] }}
                </button>
                @endforeach
            </nav>
        </div>

        <!-- Conteúdo das abas -->
        <div class="flex-1">

            <!-- Aba E-mail -->
            <div x-show="activeTab === 'email'">
                <form method="POST" action="{{ route('configuracoes.update') }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="secao" value="email">

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-4">
                        <h3 class="text-base font-semibold text-gray-900 mb-4">Configurações de E-mail (SMTP)</h3>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Driver de E-mail</label>
                                <select name="mail_driver" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="smtp" {{ ($config['mail_driver'] ?? 'smtp') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                    <option value="sendmail" {{ ($config['mail_driver'] ?? '') == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                    <option value="log" {{ ($config['mail_driver'] ?? '') == 'log' ? 'selected' : '' }}>Log (desenvolvimento)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Host SMTP</label>
                                <input type="text" name="mail_host" value="{{ $config['mail_host'] ?? '' }}" placeholder="smtp.gmail.com"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Porta SMTP</label>
                                <input type="number" name="mail_port" value="{{ $config['mail_port'] ?? 587 }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Usuário SMTP</label>
                                <input type="text" name="mail_username" value="{{ $config['mail_username'] ?? '' }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Senha SMTP</label>
                                <input type="password" name="mail_password" placeholder="••••••••"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Criptografia</label>
                                <select name="mail_encryption" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="tls" {{ ($config['mail_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ ($config['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="" {{ ($config['mail_encryption'] ?? '') == '' ? 'selected' : '' }}>Nenhuma</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Remetente</label>
                                <input type="text" name="mail_from_name" value="{{ $config['mail_from_name'] ?? 'Tallents Gestão' }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">E-mail do Remetente</label>
                                <input type="email" name="mail_from_address" value="{{ $config['mail_from_address'] ?? '' }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        <button type="button"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Testar Envio
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                            Salvar Configurações
                        </button>
                    </div>
                </form>
            </div>

            <!-- Aba Empresa -->
            <div x-show="activeTab === 'empresa'" x-cloak>
                <form method="POST" action="{{ route('configuracoes.update') }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="secao" value="empresa">

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-4">
                        <h3 class="text-base font-semibold text-gray-900 mb-4">Dados da Empresa</h3>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nome da Empresa</label>
                                <input type="text" name="empresa_nome" value="{{ $config['empresa_nome'] ?? 'Tallents Gestão' }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">CNPJ</label>
                                <input type="text" name="empresa_cnpj" value="{{ $config['empresa_cnpj'] ?? '' }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                                <input type="text" name="empresa_telefone" value="{{ $config['empresa_telefone'] ?? '' }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end mt-4">
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                            Salvar Configurações
                        </button>
                    </div>
                </form>
            </div>

            <!-- Aba Sistema -->
            <div x-show="activeTab === 'sistema'" x-cloak>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Informações do Sistema</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between py-2 border-b border-gray-50">
                            <dt class="text-sm text-gray-500">Versão</dt>
                            <dd class="text-sm font-medium text-gray-900">Tallents Gestão v1.0.0</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-50">
                            <dt class="text-sm text-gray-500">PHP</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ PHP_VERSION }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-50">
                            <dt class="text-sm text-gray-500">Laravel</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ app()->version() }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-50">
                            <dt class="text-sm text-gray-500">Ambiente</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ app()->environment() }}</dd>
                        </div>
                        <div class="flex justify-between py-2">
                            <dt class="text-sm text-gray-500">Banco de Dados</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ config('database.default') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
