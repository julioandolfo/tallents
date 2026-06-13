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
document.addEventListener('DOMContentLoaded', () => initSelects());

Alpine.start();
