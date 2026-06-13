import './bootstrap';

import Alpine from 'alpinejs';
import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.css';

window.Alpine = Alpine;
window.TomSelect = TomSelect;

/**
 * Transforma os <select> em campos com busca (estilo select2), exceto os
 * marcados com data-no-search ou multiple. Reaproveitável para conteúdo
 * inserido dinamicamente via window.initSelects(root).
 */
function initSelects(root = document) {
    root.querySelectorAll('select:not([data-no-search]):not([multiple])').forEach((el) => {
        if (el.tomselect) return;
        const vazio = el.querySelector('option[value=""]');
        new TomSelect(el, {
            create: false,
            allowEmptyOption: true,
            maxOptions: null,
            placeholder: el.dataset.placeholder || (vazio ? vazio.textContent.trim() : 'Selecione...'),
            render: {
                no_results: () => '<div class="no-results">Nenhum resultado encontrado</div>',
            },
        });
    });
}

window.initSelects = initSelects;

/* ==========================================================================
   Máscara de moeda brasileira (R$) para inputs [data-money]
   Exibe 1.234,56 enquanto digita; ao enviar o formulário, normaliza para
   número com ponto decimal (1234.56) para passar na validação `numeric`.
   ========================================================================== */
function moneyParse(str) {
    if (str == null) return '';
    str = String(str).trim();
    if (str === '') return '';
    if (str.includes(',')) str = str.replace(/\./g, '').replace(',', '.');
    const n = parseFloat(str);
    return isNaN(n) ? '' : n;
}

function moneyFormat(num) {
    if (num === '' || num == null || isNaN(num)) return '';
    return Number(num).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function moneyMaskFromTyping(value) {
    let digits = String(value).replace(/\D/g, '');
    if (digits === '') return '';
    digits = digits.replace(/^0+/, '') || '0';
    while (digits.length < 3) digits = '0' + digits;
    const cents = digits.slice(-2);
    let int = digits.slice(0, -2).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    return int + ',' + cents;
}

function initMoneyMasks(root = document) {
    root.querySelectorAll('input[data-money]').forEach((el) => {
        if (el.dataset.moneyInit) return;
        el.dataset.moneyInit = '1';
        el.type = 'text';
        el.setAttribute('inputmode', 'decimal');
        if (el.value !== '') {
            const n = moneyParse(el.value);
            el.value = n === '' ? '' : moneyFormat(n);
        }
        el.addEventListener('input', () => { el.value = moneyMaskFromTyping(el.value); });

        // Normaliza no envio do formulário associado (cobre form aninhado e form="id").
        const form = el.form;
        if (form && !form.dataset.moneyHook) {
            form.dataset.moneyHook = '1';
            form.addEventListener('submit', () => {
                form.querySelectorAll('input[data-money]').forEach((m) => {
                    const n = moneyParse(m.value);
                    m.value = n === '' ? '' : String(n);
                });
                // Inputs ligados por atributo form="id" mas fora do <form>.
                document.querySelectorAll('input[data-money][form="' + form.id + '"]').forEach((m) => {
                    const n = moneyParse(m.value);
                    m.value = n === '' ? '' : String(n);
                });
            });
        }
    });
}

window.initMoneyMasks = initMoneyMasks;

document.addEventListener('DOMContentLoaded', () => { initSelects(); initMoneyMasks(); });

Alpine.start();
