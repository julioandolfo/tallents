@extends('layouts.app')

@section('title', 'Meu Perfil')
@section('page-title', 'Meu Perfil')

@section('content')
<div class="py-4 max-w-3xl" x-data="{
    fotoPreview: {{ auth()->user()->foto ? '\'' . Storage::url(auth()->user()->foto) . '\'' : 'null' }},
    setFoto(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => { this.fotoPreview = e.target.result; };
            reader.readAsDataURL(file);
        }
    }
}">

    <!-- Card de perfil -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 h-20"></div>
        <div class="px-6 pb-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-end gap-4 -mt-10">
                <div class="relative shrink-0">
                    <div class="h-20 w-20 rounded-full overflow-hidden bg-indigo-100 ring-4 ring-white shadow">
                        <template x-if="fotoPreview">
                            <img :src="fotoPreview" class="h-full w-full object-cover">
                        </template>
                        <template x-if="!fotoPreview">
                            <div class="h-full w-full flex items-center justify-center">
                                <span class="text-indigo-700 text-2xl font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="flex-1 pt-2">
                    <h2 class="text-xl font-bold text-gray-900">{{ auth()->user()->name }}</h2>
                    <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário de dados -->
    <div class="space-y-6">

        <!-- Dados Pessoais -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Informações Pessoais</h3>
            </div>
            <form method="POST" action="{{ route('perfil.update') }}" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <!-- Foto -->
                <div class="flex items-center gap-4">
                    <label class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-lg transition">
                        <input type="file" name="foto" accept="image/*" class="hidden" @change="setFoto($event)">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Alterar Foto
                    </label>
                    <p class="text-xs text-gray-400">JPG, PNG — Máx. 2MB</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                        Salvar Informações
                    </button>
                </div>
            </form>
        </div>

        <!-- Alterar Senha -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Alterar Senha</h3>
            </div>
            <form method="POST" action="{{ route('perfil.password') }}" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Senha Atual</label>
                    <input type="password" name="current_password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nova Senha</label>
                    <input type="password" name="password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nova Senha</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                        Alterar Senha
                    </button>
                </div>
            </form>
        </div>

        <!-- Zona de perigo -->
        <div class="bg-white rounded-lg shadow-sm border border-red-200">
            <div class="px-6 py-4 border-b border-red-100">
                <h3 class="text-base font-semibold text-red-700">Zona de Perigo</h3>
            </div>
            <div class="p-6">
                <p class="text-sm text-gray-600 mb-4">Uma vez que a sua conta for excluída, todos os seus dados serão removidos permanentemente.</p>
                <form method="POST" action="{{ route('perfil.destroy') }}"
                      x-data
                      @submit.prevent="if(confirm('Tem certeza? Esta ação não pode ser desfeita.')) $el.submit()">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                        Excluir minha conta
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
