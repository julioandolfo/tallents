{{-- Aplica o tema salvo ANTES da renderização para evitar "flash" de tela clara.
     Valores possíveis em localStorage.theme: 'light' | 'dark' | 'system' (ou ausente = system). --}}
<script>
    (function () {
        try {
            var pref = localStorage.getItem('theme') || 'system';
            var systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            var isDark = pref === 'dark' || (pref === 'system' && systemDark);
            document.documentElement.classList.toggle('dark', isDark);
        } catch (e) {}
    })();
</script>
