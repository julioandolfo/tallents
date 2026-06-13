@push('scripts')
<script>
(function () {
    const btn = document.querySelector('[data-cnpj-buscar]');
    if (!btn) return;
    const campo = document.querySelector('input[name="cnpj"]');
    const msg = document.querySelector('[data-cnpj-msg]');
    const set = (name, val) => { const el = document.querySelector('[name="' + name + '"]'); if (el && val) el.value = val; };
    btn.addEventListener('click', async () => {
        const cnpj = (campo.value || '').replace(/\D/g, '');
        if (cnpj.length !== 14) { msg.textContent = 'Informe 14 dígitos.'; msg.className = 'text-xs text-red-500 mt-1'; return; }
        msg.textContent = 'Consultando...'; msg.className = 'text-xs text-gray-400 mt-1';
        try {
            const r = await fetch('/api/cnpj/' + cnpj, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
            const d = await r.json();
            if (!r.ok) { msg.textContent = d.error || 'Não encontrado.'; msg.className = 'text-xs text-red-500 mt-1'; return; }
            set('nome', d.fantasia || d.nome);
            set('razao_social', d.nome);
            set('email', d.email);
            set('telefone', d.telefone);
            set('cep', d.cep);
            set('logradouro', d.logradouro);
            set('numero', d.numero);
            set('complemento', d.complemento);
            set('bairro', d.bairro);
            set('cidade', d.municipio);
            const uf = document.querySelector('[name="estado"]'); if (uf && d.uf) uf.value = d.uf;
            msg.textContent = 'Dados preenchidos a partir do CNPJ.'; msg.className = 'text-xs text-emerald-600 mt-1';
        } catch (e) { msg.textContent = 'Falha na consulta.'; msg.className = 'text-xs text-red-500 mt-1'; }
    });
})();
</script>
@endpush
