{{-- Dashboard --}}
<a href="{{ route('dashboard') }}"
   class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
    <svg class="mr-3 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
    </svg>
    Dashboard
</a>

{{-- Cadastros --}}
<div class="pt-4">
    <p class="px-2 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Cadastros</p>
    <div class="mt-2 space-y-1">
        <a href="{{ route('colaboradores.index') }}"
           class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('colaboradores.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Colaboradores
        </a>
        <a href="{{ route('empresas.index') }}"
           class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('empresas.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Empresas
        </a>
        <a href="{{ route('setores.index') }}"
           class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('setores.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            Setores
        </a>
        <a href="{{ route('cargos.index') }}"
           class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('cargos.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Cargos
        </a>
    </div>
</div>

{{-- Gestão RH --}}
<div class="pt-4">
    <p class="px-2 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Gestão RH</p>
    <div class="mt-2 space-y-1">
        <a href="{{ route('ocorrencias.index') }}"
           class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('ocorrencias.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Ocorrências
        </a>
        <a href="{{ route('horas-extras.index') }}"
           class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('horas-extras.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Horas Extras
        </a>
        <a href="{{ route('promocoes.index') }}"
           class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('promocoes.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
            Promoções
        </a>
        <a href="{{ route('fechamentos.index') }}"
           class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('fechamentos.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            Fechamento
        </a>
    </div>
</div>

{{-- Administração --}}
@if(auth()->check() && method_exists(auth()->user(), 'isAdmin') && (auth()->user()->isAdmin() || auth()->user()->isRH()))
<div class="pt-4">
    <p class="px-2 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Administração</p>
    <div class="mt-2 space-y-1">
        <a href="{{ route('usuarios.index') }}"
           class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('usuarios.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Usuários
        </a>
        <a href="{{ route('tipos-ocorrencias.index') }}"
           class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('tipos-ocorrencias.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Tipos de Ocorrência
        </a>
        <a href="{{ route('tipos-bonus.index') }}"
           class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('tipos-bonus.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Tipos de Bônus
        </a>
        <a href="{{ route('niveis-hierarquicos.index') }}"
           class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('niveis-hierarquicos.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
            </svg>
            Níveis Hierárquicos
        </a>
        <a href="{{ route('configuracoes.index') }}"
           class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('configuracoes.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Configurações
        </a>
    </div>
</div>
@endif
