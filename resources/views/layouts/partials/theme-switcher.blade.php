{{-- Seletor de tema: Claro / Escuro / Sistema. Persiste em localStorage e
     reage à mudança do tema do sistema operacional quando em modo "Sistema". --}}
<div
    x-data="{
        open: false,
        theme: localStorage.getItem('theme') || 'system',
        apply() {
            const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const isDark = this.theme === 'dark' || (this.theme === 'system' && systemDark);
            document.documentElement.classList.toggle('dark', isDark);
        },
        set(v) {
            this.theme = v;
            localStorage.setItem('theme', v);
            this.apply();
            this.open = false;
        },
    }"
    x-init="apply(); window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => { if (theme === 'system') apply(); })"
    class="relative"
>
    <button @click="open = !open" type="button"
            class="flex items-center justify-center h-9 w-9 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none"
            title="Tema">
        {{-- Sol (claro) --}}
        <svg x-show="theme === 'light'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        {{-- Lua (escuro) --}}
        <svg x-show="theme === 'dark'" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
        </svg>
        {{-- Monitor (sistema) --}}
        <svg x-show="theme === 'system'" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
    </button>

    <div x-show="open" x-cloak @click.away="open = false"
         class="absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg py-1 ring-1 ring-black ring-opacity-5 z-50">
        <template x-for="opt in [
            { v: 'light',  label: 'Claro' },
            { v: 'dark',   label: 'Escuro' },
            { v: 'system', label: 'Sistema' }
        ]" :key="opt.v">
            <button type="button" @click="set(opt.v)"
                    class="flex items-center justify-between w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    :class="theme === opt.v ? 'font-semibold text-indigo-600' : ''">
                <span x-text="opt.label"></span>
                <svg x-show="theme === opt.v" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </button>
        </template>
    </div>
</div>
