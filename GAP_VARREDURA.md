# Varredura de Paridade — Sistema Antigo (rh-privus) → Novo (Tallents)

> Auditoria tela-a-tela do sistema antigo comparada ao novo. Gerada a partir da
> leitura do código-fonte dos dois sistemas. Status: ✅ existe · ⚠️ parcial · ❌ falta.
> Os detalhes por tela estão nas seções A/B/C/D ao final deste documento.

## Resumo executivo — gaps transversais (afetam várias telas)

| # | Gap | Impacto | Onde |
|---|-----|---------|------|
| T1 | **E-mail transacional nunca dispara** — não há nenhum Mailable no projeto; a config SMTP salva no banco não é aplicada ao mailer e o "Testar Envio" é decorativo. Antigo enviava: boas-vindas, promoção, fechamento, ocorrência. | Alto | Colaboradores, Promoções, Fechamento, Ocorrências, Config E-mail |
| T2 | **Escopo por papel ausente** — RH deveria ver só a própria empresa; GESTOR só o setor; COLABORADOR só a si. Hoje tudo é global → vazamento entre empresas. | Alto (segurança) | Empresas, Setores, Cargos, Colaboradores, Ocorrências, Dashboard, Relatório |
| T3 | **Permissões ADMIN-only** — Usuários e Config de E-mail deveriam ser só ADMIN (hoje RH/GESTOR acessam). | Alto (segurança) | Usuários, Config E-mail |
| T4 | **APIs existem mas não ligadas às telas** — dropdowns dependentes (empresa→setor→cargo→líder), auto-preenchimento de CNPJ e aba de bônus têm backend (parcial/quebrado) mas nenhuma view consome. | Médio | Colaborador add/edit, Empresas, Fechamento |
| T5 | **Exclusões sem checar vínculos** — níveis hierárquicos, tipos de ocorrência e tipos de bônus apagam direto → registros órfãos. Antigo bloqueava. | Médio | Níveis, Tipos de Ocorrência, Tipos de Bônus |
| T6 | **Exportações ausentes** — CSV/PDF/impressão em listagens e relatórios. | Médio | Ocorrências, Relatório, Fechamento, Colaboradores |
| T7 | **Bugs de filtro/campos** — busca de Empresas (`busca` vs `search`), filtros de Ocorrências (`busca`/`tipo_id` vs `colaborador_id`/`tipo_ocorrencia_id`), Minha Conta senha (`current_password` vs `senha_atual`). | Alto (quebrado) | Empresas, Ocorrências, Minha Conta |
| T8 | **Imports/queries quebrados** — `BonusApiController` e `FechamentoPagamentoController` usam `App\Models\Bonus` (inexistente → `ColaboradorBonus`) e validam tabela `tipo_bonus` (certo: `tipos_bonus`). | Alto (runtime) | Fechamento, API Bônus |

## Gaps por módulo (destaques)

- **Dashboard**: 3 gráficos Chart.js (ocorrências/mês, colaboradores/status, ocorrências/tipo) + tabela "Ranking de Ocorrências" (top 10) — ❌ inexistentes.
- **Fechamento de Pagamentos**: edição manual por colaborador (descontos/adicionais), seleção por checkbox, cálculo de bônus por janela de datas, fórmula `salário + HE + bônus − descontos + adicionais` — ⚠️/❌ (hoje só botão "Fechar").
- **Bônus por colaborador**: schema sem `data_inicio/data_fim/observacoes` e com unique que impede múltiplos; falta aba/modal no perfil.
- **Templates de e-mail**: CRUD + editor + variáveis + 4 disparos automáticos — ❌ (models órfãos).
- **OneSignal/Push**: config + tela de envio — ❌ (models órfãos).
- **Hora extra**: preview ao vivo do valor; **Promoção**: auto-load do salário atual — ⚠️.

## Plano de execução em ondas (proposta priorizada)

| Onda | Tema | Esforço | Por que primeiro |
|------|------|---------|------------------|
| **0** | Bugs/correções rápidas (T7, T8, T5) | Baixo | Coisas quebradas em runtime e buscas que não funcionam |
| **1** | E-mail transacional (T1) — aplicar SMTP do banco + Mailables + testar envio | Médio | Funcionalidade central do antigo, hoje 100% ausente |
| **2** | Fechamento de Pagamentos completo + bônus por colaborador | Alto | Núcleo financeiro do RH |
| **3** | UX dinâmica (T4) — dropdowns dependentes, auto-CNPJ, preview HE, auto-salário | Médio | Liga o backend já existente às telas |
| **4** | Dashboard (gráficos + ranking) | Médio | Alto valor visual/gestão |
| **5** | Escopo por papel (T2, T3) | Alto | Segurança/multi-tenant |
| **6** | Templates de e-mail + Push/OneSignal (config-driven) | Alto | Depende de credenciais (você insere depois) |
| **7** | Exportações (CSV/PDF) + colunas de status + filtros faltantes | Médio | Acabamento |

---

# Auditoria de Paridade — Estrutura (Empresas, Setores, Cargos, Hierarquia, Níveis, Dashboard + APIs)

Sistema ANTIGO: `/home/user/rh-privus/` (PHP puro)
Sistema NOVO: `/home/user/tallents/` (Laravel)
Data: 2026-06-13

Legenda de status: ✅ existe / ⚠️ parcial / ❌ falta

---

## 1. pages/empresas.php

Arquivo novo: `app/Http/Controllers/EmpresaController.php`, `resources/views/empresas/{index,create,edit,show}.blade.php`

| Funcionalidade | Antigo (comportamento) | Status no novo | Como implementar |
|---|---|---|---|
| Listar empresas | Tabela com colunas ID, Nome Fantasia, Razão Social, CNPJ, Telefone, Email, Cidade/UF, Status, Ações | ⚠️ | Novo lista Nome, CNPJ, Cidade/UF, Colaboradores, Ações. Faltam colunas: Razão Social (existe como subtítulo), **Telefone**, **Email**, **Status (badge Ativo/Inativo)**. Adicionar colunas Telefone, Email e badge de status (`$empresa->ativa`) na tabela `empresas/index.blade.php`. |
| Criar empresa | Modal com action=add; campos nome_fantasia, razao_social, cnpj, telefone, email, cidade, estado, status, percentual_hora_extra | ✅ | Novo tem create.blade.php com todos esses campos + endereço completo (cep, logradouro, etc) + logo. Mais completo que o antigo. |
| Editar empresa | `editarEmpresa()` preenche modal via JSON, action=edit | ✅ | Novo usa rota edit dedicada. |
| Excluir empresa | action=delete, SweetAlert de confirmação "Tem certeza que deseja excluir X?" | ✅ | Novo usa `confirm()` nativo + DELETE. Funcional (visual mais simples). |
| Campo % Adicional Hora Extra | `percentual_hora_extra`, default 50.00; máscara `#0,00`; usado no cálculo de horas extras | ✅ | Presente em create/edit (`percentual_hora_extra`, default 50, step 0.01). |
| Busca/filtro (texto) | Input busca client-side via DataTables (filtra todas as colunas em tempo real, keyup) | ⚠️ | Novo tem busca server-side por nome ou CNPJ. **BUG: o form usa `name="busca"` mas o controller lê `$request->search`** (linha 13 do controller vs linha 24 da view) → a busca não funciona. Renomear input para `name="search"` OU ajustar controller para `$request->busca`. |
| Restrição por papel (ADMIN vê todas; RH vê só a própria empresa) | RH só vê/edita a empresa dele; botão "Nova Empresa" só para ADMIN; coluna Ações só para ADMIN | ❌ | Novo `EmpresaController@index` lista TODAS as empresas sem filtrar por papel/empresa do usuário, e não esconde "Nova Empresa"/ações para RH. Implementar scoping por papel: se usuário for RH, filtrar `where('id', $user->empresa_id)` e ocultar criar/editar/excluir. |
| Máscara de CNPJ no input | jQuery Mask `00.000.000/0000-00` | ⚠️ | Novo tem placeholder mas **sem máscara JS**. Adicionar máscara (Alpine/inputmask) no input cnpj. |
| Máscara de telefone | jQuery Mask dinâmica (fixo `(00) 0000-00000` / cel `(00) 00000-0000`) | ⚠️ | Novo não tem máscara de telefone. Adicionar. |
| Validação de CNPJ (dígitos verificadores) | JS `validarCNPJ()` valida DV e rejeita CNPJ inválido antes de submeter | ⚠️ | Novo só valida `string|max:20` no backend. Não valida dígitos verificadores nem formato. Adicionar validação de CNPJ (regra/Rule customizada). |
| Validação de Email no submit | JS `validarEmail()` regex | ✅ | Novo valida `email` no backend (`nullable|email`). |
| Validação de telefone (min 10 dígitos) | JS verifica ≥10 dígitos | ⚠️ | Novo não valida formato de telefone. Opcional. |
| Formatação CNPJ/telefone na exibição | `formatar_cnpj()`, `formatar_telefone()` na tabela | ⚠️ | Novo exibe CNPJ cru (sem formatação). Telefone não é exibido na lista. Aplicar formatação na view. |

---

## 2. pages/setores.php

Arquivo novo: `app/Http/Controllers/SetorController.php`, `resources/views/setores/{index,create,edit}.blade.php`

| Funcionalidade | Antigo (comportamento) | Status no novo | Como implementar |
|---|---|---|---|
| Listar setores | Colunas ID, Empresa (só ADMIN), Nome do Setor, Descrição, Status, Ações | ⚠️ | Novo: Nome, Empresa, Descrição, Colaboradores, Ações. Falta **coluna Status (badge Ativo/Inativo)** — o model tem `ativo` mas a lista não exibe. Adicionar badge na index. |
| Criar setor | Modal: empresa_id (select só ADMIN; hidden p/ RH), nome_setor, descricao, status | ✅ | Novo create tem empresa_id, nome, descricao, ativo. Campo equivalente. |
| Editar setor | `editarSetor()` via JSON, action=edit | ✅ | Rota edit dedicada. |
| Excluir setor | action=delete + SweetAlert | ✅ | confirm() + DELETE. |
| Busca/filtro texto | DataTables client-side (todas colunas) | ⚠️ | Novo: o controller suporta `?search=` e `?empresa_id=`, **mas a view index não tem formulário de busca nem filtro de empresa**. Adicionar inputs de busca e select de empresa que enviam `search`/`empresa_id`. |
| Campo Status | `status` ativo/inativo (select) | ✅ | Novo usa `ativo` (boolean). Equivalente. |
| Restrição por papel | RH só vê setores da própria empresa; select de empresa fixado | ❌ | Novo lista todos os setores de todas as empresas sem scoping por papel. Implementar filtro por empresa do usuário quando RH. |

---

## 3. pages/cargos.php

Arquivo novo: `app/Http/Controllers/CargoController.php`, `resources/views/cargos/{index,create,edit}.blade.php`

| Funcionalidade | Antigo (comportamento) | Status no novo | Como implementar |
|---|---|---|---|
| Listar cargos | Colunas ID, Empresa (só ADMIN), Nome do Cargo, Descrição, Salário Base, Status, Ações | ⚠️ | Novo: Cargo, Nível, Salário Base, Colaboradores, Ações. Faltam: **coluna Empresa**, **coluna Descrição**, **coluna Status (badge)**. Adicionar. (Novo acrescenta coluna Nível, que é melhoria.) |
| Criar cargo | Modal: empresa_id, nome_cargo, descricao, salario_base (opcional), status. salario_base convertido de "1.234,56"→1234.56 | ✅ | Novo create tem empresa_id, nome, descricao, salario_base, salario_maximo, nivel_hierarquico_id, ativo. Mais completo. |
| Editar cargo | `editarCargo()` formata salário pt-BR no modal | ✅ | Rota edit dedicada. |
| Excluir cargo | action=delete + SweetAlert | ✅ | confirm() + DELETE. |
| Salário Base — máscara/moeda | jQuery Mask `#.##0,00`; exibição via `formatar_moeda()` | ✅ | Novo: input `type=number step` no form; exibição `R$ ' . number_format()`. Equivalente (sem máscara visual ao digitar, mas funcional). |
| Busca/filtro texto | DataTables client-side | ⚠️ | Controller suporta `?search=`/`?empresa_id=` mas a **view index não tem campo de busca nem filtro de empresa**. Adicionar. |
| Restrição por papel | RH só vê cargos da própria empresa | ❌ | Novo não faz scoping por papel/empresa. Implementar filtro quando RH. |
| empresa_id obrigatório | Antigo exige empresa_id (redirect erro se vazio) | ⚠️ | Novo valida `empresa_id` como `nullable` e usa `resolveEmpresaId()`. Comportamento difere (permite cargo sem empresa). Verificar regra de negócio desejada. |

---

## 4. pages/hierarquia.php (Organograma)

Arquivo novo: `app/Http/Controllers/OrganogramaController.php`, `resources/views/organograma/index.blade.php`

| Funcionalidade | Antigo (comportamento) | Status no novo | Como implementar |
|---|---|---|---|
| Visualização do organograma | Agrupa colaboradores ativos **por nível hierárquico** (`nh.nivel`), ordena por nível ASC e nome; cards com inicial, nome (link p/ view), cargo, setor e líder | ⚠️ | Novo monta árvore **por líder (lider_id)** em vez de agrupar por nível hierárquico. Abordagem diferente: o antigo mostra faixas horizontais por nível (Diretoria, Gerência...). Se a regra desejada é "níveis", reimplementar agrupamento por `nivel_hierarquico_id`. Caso contrário, manter (árvore por líder é válida) mas documentar divergência. |
| Filtro por Empresa | Select "Todas" + empresas ativas (só ADMIN); filtro via GET `?empresa=` | ⚠️ | Novo tem filtro de empresa, mas **default = primeira empresa** (não "Todas"); não há opção "todas as empresas". Ajustar para permitir visão consolidada. |
| Filtro por Setor | Select de setores ativos; GET `?setor=` | ❌ | Novo **não tem filtro por setor**. Adicionar select de setor no organograma e filtrar colaboradores por `setor_id`. |
| Botão "Limpar" filtros | Link reset para hierarquia.php | ⚠️ | Verificar se novo tem reset; provavelmente ausente. Adicionar. |
| Botão "Gerenciar Níveis" | Link para niveis_hierarquicos.php no topo | ⚠️ | Conferir/adicionar atalho para níveis-hierarquicos no organograma. |
| Restrição por papel | RH limitado à própria empresa (sem select) | ❌ | Garantir scoping por papel no OrganogramaController. |
| Empty state | Mensagem + botão "Cadastrar Colaboradores" quando vazio | ✅/⚠️ | Conferir empty state na view nova. |
| Exibe líder no card | Mostra nome do líder de cada colaborador | ⚠️ | Como o novo usa árvore por líder, a relação aparece pela estrutura; conferir se nome do líder é exibido. |

---

## 5. pages/niveis_hierarquicos.php

Arquivo novo: `app/Http/Controllers/NivelHierarquicoController.php`, `resources/views/niveis-hierarquicos/{index,create,edit}.blade.php`, model `app/Models/NivelHierarquico.php`

| Funcionalidade | Antigo (comportamento) | Status no novo | Como implementar |
|---|---|---|---|
| Listar níveis | Colunas ID, Nível (badge), Nome, **Código** (badge), Descrição, Status, Ações; ordenado por nível ASC | ⚠️ | Novo lista Ordem, Nome, Descrição, Cargos, Ações. Faltam: **coluna Código**, **coluna Status**. |
| Campo `codigo` (único) | Campo obrigatório, único; usado como `nivel_codigo` na hierarquia; valida duplicidade ("Código já existe!") | ❌ | Novo **não tem campo `codigo`** (model fillable só nome, nivel, descricao, empresa_id). Adicionar coluna `codigo` na migration, fillable, form e validação `unique:niveis_hierarquicos,codigo`. |
| Campo `nivel` (numérico, 1=mais alto) | Input number obrigatório, min 1; usado para ordenar hierarquia | ⚠️ | Novo usa `ordem` no form mas mapeia para `nivel` no controller (`$data['nivel'] = ordem ?? 1`). Funciona, mas o label "Ordem" diverge de "Nível". Manter consistência (nome do campo). |
| Validação código único | Antigo: SELECT antes de inserir/atualizar, erro se duplicado | ❌ | Reintroduzir junto com o campo `codigo` (regra unique ignorando o próprio id no update). |
| Criar/Editar | Campos nome, codigo, nivel, descricao, status | ⚠️ | Novo cria/edita nome, ordem/nivel, descricao (+ empresa_id). Falta codigo e status. |
| **Proteção de exclusão** | Antigo: antes de excluir, conta colaboradores com `nivel_hierarquico_id = ?`. Se >0, BLOQUEIA: "Não é possível excluir: existem N colaborador(es) usando este nível!" | ❌ | Novo `destroy()` apaga direto sem verificar dependências. Implementar: contar `Colaborador::where('nivel_hierarquico_id',$id)->count()` e abortar com mensagem se houver vínculos. |
| Campo Status (ativo/inativo) | Select status | ❌ | Model novo não tem `status`/`ativo` para níveis. Adicionar se quiser paridade. |
| Escopo de níveis | Antigo: níveis são **globais** (sem empresa_id) | ⚠️ | Novo adiciona `empresa_id` (níveis por empresa) — mudança de modelo de dados. Decisão de design; documentar. |
| Busca | DataTables client-side | ⚠️ | Controller suporta `?search=`; **view index sem campo de busca**. Adicionar. |

---

## 6. pages/dashboard.php

Arquivo novo: `app/Http/Controllers/DashboardController.php`, `resources/views/dashboard/index.blade.php`

| Funcionalidade | Antigo (comportamento) | Status no novo | Como implementar |
|---|---|---|---|
| Card "Total de Colaboradores" | Contagem (scoped por papel: ADMIN=todos, RH=empresa, GESTOR=setor) | ⚠️ | Novo exibe total global, **sem scoping por papel** (RH/GESTOR veem todos). Aplicar filtro por papel. |
| Card "Colaboradores Ativos" | Count status='ativo' scoped | ⚠️ | Novo mostra "X ativos" como subtítulo do card Colaboradores. Sem scoping por papel. |
| Card "Ocorrências no Mês" | Count ocorrências do mês atual, scoped | ⚠️ | Novo tem card Ocorrências no mês, mas global. Aplicar scoping. |
| Card "Inativos" | total - ativos, link p/ colaboradores?status=inativo | ❌ | Novo **não tem card de Inativos**. Tem em troca cards "Empresas" e "Horas extras (mês)" (melhorias). Adicionar card Inativos se desejado. |
| Cards clicáveis (links) | Cada card linka para listagem filtrada (colaboradores, ?status=ativo, ocorrencias_list, ?status=inativo) | ❌ | Cards novos **não são links**. Tornar cards clicáveis para as listagens correspondentes. |
| Gráfico "Ocorrências por Mês" (linha, 6 meses) | Chart.js line com labels meses M/Y e contagens, scoped | ❌ | Novo **não tem nenhum gráfico**. Implementar: 6 últimos meses, contagem de ocorrências por mês. |
| Gráfico "Colaboradores por Status" (doughnut) | Chart.js doughnut por status | ❌ | Não existe no novo. Implementar. |
| Gráfico "Ocorrências por Tipo" (barras, 30 dias) | Chart.js bar, GROUP BY tipo, últimos 30 dias, top 10 | ❌ | Não existe no novo. Implementar. |
| Tabela "Ranking de Ocorrências" (30 dias) | Top 10 colaboradores por nº de ocorrências; medalhas 🥇🥈🥉; badge colorido por faixa (>5 danger, >2 warning, senão primary) | ❌ | Novo **não tem ranking**. Implementar tabela top-10 com medalhas e badges por faixa. |
| Lista "Últimas Ocorrências" | (não existia no antigo) | ✅ (extra) | Novo adiciona — melhoria. |
| Lista "Colaboradores Recentes" | (não existia) | ✅ (extra) | Melhoria. |
| Ações Rápidas | (não existia) | ✅ (extra) | Melhoria. |
| Card "Empresas" / "Horas extras mês" | (não existia) | ✅ (extra) | Melhorias. |
| Scoping por papel (ADMIN/RH/GESTOR/COLAB) | Todas as métricas filtradas conforme papel | ❌ | Dashboard novo é único e global. Implementar variações por papel (especialmente GESTOR por setor e COLABORADOR vendo só os próprios dados). |

---

## 7. APIs AJAX

### api/buscar_cnpj.php → `app/Http/Controllers/Api/CnpjApiController.php` (rota `GET /api/cnpj/{cnpj}`)

| Funcionalidade | Antigo (comportamento) | Status no novo | Como implementar |
|---|---|---|---|
| Consulta CNPJ na ReceitaWS | GET receitaws, retorna razao_social, nome_fantasia, cep, logradouro, numero, complemento, bairro, cidade, estado, telefone, email | ✅ | Endpoint novo existe e retorna campos equivalentes (nome, fantasia, endereço, etc). |
| Fallback BrasilAPI | Se ReceitaWS falhar (timeout/HTTP≠200), tenta `brasilapi.com.br/api/cnpj/v1/{cnpj}` e mapeia campos | ❌ | Novo **não tem fallback**. Se ReceitaWS falhar retorna 502. Adicionar fallback BrasilAPI no CnpjApiController. |
| **Auto-preenchimento do formulário (front-end)** | No antigo, a tela de empresas/colaboradores chama esse endpoint e **preenche automaticamente** os campos do form a partir do CNPJ | ❌ | Novo **NÃO tem JS** em empresas/create|edit que chame `/api/cnpj` e preencha os campos. (grep confirma: nenhum script de cnpj nas views.) Implementar: ao sair do campo CNPJ (blur), fazer fetch `/api/cnpj/{cnpj}` e popular razao_social, cep, logradouro, numero, bairro, cidade, estado, telefone, email. Também colaboradores/_form. |
| Validação 14 dígitos | preg_replace + checa len 14 | ✅ | Novo valida 14 dígitos (422 se inválido). |

### api/get_setores.php → `Api/SetorApiController@index` (rota `GET /api/setores?empresa_id=`)

| Funcionalidade | Antigo | Status no novo | Como implementar |
|---|---|---|---|
| Listar setores ativos por empresa | SELECT id, nome_setor WHERE empresa_id=? AND status='ativo' | ✅ | Endpoint novo retorna id, nome, empresa_id filtrando por empresa_id e ativo. |
| **Uso em dropdown dependente empresa→setor** | Antigo: ao trocar empresa no form, recarrega setores via AJAX | ❌ | Novo `colaboradores/_form.blade.php` usa selects renderizados no servidor (todos os setores), **sem JS de dropdown dependente** (grep confirma ausência de fetch/api/setores nas views). Implementar JS: ao mudar empresa_id, fetch `/api/setores?empresa_id=` e repopular o select de setor. |

### api/get_cargos.php → `Api/CargoApiController@index` (rota `GET /api/cargos?empresa_id=`)

| Funcionalidade | Antigo | Status no novo | Como implementar |
|---|---|---|---|
| Listar cargos ativos por empresa | SELECT id, nome_cargo WHERE empresa_id=? AND status='ativo' | ✅ | Endpoint novo OK (retorna id, nome, salario_base, etc). |
| Dropdown dependente empresa→cargo | AJAX ao trocar empresa | ❌ | Igual aos setores: sem JS dependente no form. Implementar fetch `/api/cargos?empresa_id=` no change de empresa. |

### api/get_lideres.php → `Api/ColaboradorApiController@lideres` (rota `GET /api/lideres`)

| Funcionalidade | Antigo (comportamento) | Status no novo | Como implementar |
|---|---|---|---|
| Listar líderes potenciais | Filtra empresa_id, status ativo; **opcionalmente por setor_id**; **filtra por nível hierárquico superior** (`nh.nivel < (nível do colaborador)`); exclui o próprio (excluir_id); ordena por nível | ⚠️ | Novo só filtra `empresa_id` (obrigatório) + status ATIVO + `excluir_id`. **Faltam: filtro por setor_id, filtro por nível hierárquico superior, ordenação por nível, retorno de nivel_nome/nivel_numero.** Reimplementar a lógica de nível (líder deve ter nível superior ao subordinado). |
| Dropdown dependente de líder | AJAX preenche select de líder conforme empresa/setor/nível | ❌ | Form novo de colaborador usa select estático de líderes (sem JS). Implementar atualização dinâmica via `/api/lideres`. |

---

## TOP GAPS PRIORITÁRIOS

Estes são os ❌/⚠️ de maior impacto (ordem de prioridade):

1. **❌ Dashboard sem gráficos nem ranking** — o antigo tem 3 gráficos Chart.js (Ocorrências por Mês/linha 6m, Colaboradores por Status/doughnut, Ocorrências por Tipo/barras 30d) e a tabela "Ranking de Ocorrências" (top 10, medalhas 🥇🥈🥉, badges por faixa >5/>2). O novo não tem nenhum. Reimplementar no `DashboardController` + view.

2. **❌ Dependent dropdowns ausentes (empresa→setor / empresa→cargo / empresa+setor+nível→líder)** — os endpoints `/api/setores`, `/api/cargos`, `/api/lideres` existem, mas NENHUMA view os chama via JS. O form de colaborador usa selects estáticos. Implementar JS `change` em empresa_id para repopular setor, cargo e líder.

3. **❌ Auto-preenchimento de CNPJ no formulário** — endpoint `/api/cnpj/{cnpj}` existe, mas as views de empresa (e colaborador) não têm JS que chame e preencha os campos automaticamente. Implementar fetch no blur do CNPJ. (Bônus: adicionar fallback BrasilAPI no controller, hoje só ReceitaWS.)

4. **❌ Níveis Hierárquicos — perda de funcionalidades**: (a) campo `codigo` único removido (model/migration/form/validação); (b) **exclusão não bloqueia quando há colaboradores usando o nível** (antigo conta e impede — risco de dados órfãos); (c) sem coluna Código/Status na listagem. Itens (b) é o mais crítico (integridade).

5. **❌ Ausência de scoping por papel (RH/GESTOR)** em Empresas, Setores, Cargos, Hierarquia e Dashboard — o antigo restringe RH à própria empresa e GESTOR ao próprio setor; o novo lista tudo globalmente. Risco de vazamento de dados entre empresas. Aplicar filtros por papel nos controllers.

6. **⚠️ BUG na busca de Empresas** — o form envia `name="busca"` mas o controller lê `$request->search` → a busca não filtra nada. Corrigir o nome do parâmetro (1 linha).

7. **⚠️ Filtro por Setor ausente no Organograma** e default não-consolidado (mostra só a 1ª empresa, sem opção "Todas"). Adicionar select de setor e opção "todas".

8. **⚠️ Colunas de Status (Ativo/Inativo) ausentes** nas listagens de Empresas, Setores, Cargos e Níveis (os campos `ativa`/`ativo` existem nos models mas não são exibidos). Adicionar badges.

9. **⚠️ Buscas/filtros sem UI** em Setores, Cargos e Níveis — controllers suportam `?search=`/`?empresa_id=` mas as views index não têm os inputs. Adicionar campos de busca + filtro de empresa.

10. **⚠️ Líderes — regra de nível hierárquico perdida** — o antigo só permite como líder quem tem nível hierárquico superior ao subordinado (`nh.nivel < nível do colaborador`) e filtra por setor; o novo ignora isso. Reimplementar para evitar líder de nível inferior/igual.
# Auditoria de Paridade — Módulo Colaboradores (Sistema Antigo PHP → Novo Laravel)

Data: 2026-06-13
Antigo: `/home/user/rh-privus/` · Novo: `/home/user/tallents/`

Legenda: ✅ existe · ⚠️ parcial · ❌ falta

> NOTA IMPORTANTE: o sistema novo já possui um **scaffolding de APIs em `routes/api.php`** (`/api/colaboradores`, `/api/setores`, `/api/cargos`, `/api/lideres`, `/api/bonus`, `/api/cnpj`) com controllers em `app/Http/Controllers/Api/`. Porém: (a) **nenhuma dessas APIs está conectada às views Blade** (forms usam selects estáticos, não há botão Sincronizar nem aba de bônus); e (b) o `BonusApiController` e o `FechamentoPagamentoController` contêm **bugs**: importam um model inexistente `App\Models\Bonus` (deveria ser `ColaboradorBonus`), validam a tabela `tipo_bonus` (nome errado, é `tipos_bonus`) e usam campos `descricao/registrado_por/ativo` que não existem no schema de `colaboradores_bonus`; falta também método `update` (edição) de bônus. Logo, vários itens estão "⚠️ backend parcial/quebrado + não integrado" em vez de totalmente ausentes.

---

## 1. pages/colaboradores.php (Listagem)

| Funcionalidade | Antigo (comportamento) | Status no novo | Como implementar |
|---|---|---|---|
| Listagem com avatar/foto | Tabela com símbolo circular: foto em `../uploads/fotos/<foto>` ou inicial do nome | ✅ | — |
| Busca textual | Input client-side via DataTables (`search`) sobre toda a tabela | ✅ (server-side por nome/cpf em `index`) | OK |
| Filtro Empresa (ADMIN) | Select de empresas ativas; só ADMIN | ✅ | — |
| Filtro Setor | Select setores (escopo por role) | ⚠️ Controller só filtra `empresa_id` e `status`; não há filtro por `setor_id` | Adicionar `when($request->setor_id,...)` no `ColaboradorController@index` e o select na view |
| Filtro Status (ativo/pausado/desligado) | Select status | ✅ | Conferir valores (antigo usa minúsculas `ativo`; novo usa `ATIVO`) |
| Escopo por role (RH=empresa, GESTOR=setor) | RH vê só sua empresa; GESTOR vê só seu setor; COLABORADOR é redirecionado para próprio view | ⚠️ Não há escopo por role no `index` (lista tudo) | Aplicar policy/escopo: RH→empresa do usuário, GESTOR→setor, COLABORADOR→redirect para `show` próprio |
| Colunas: ID, Empresa(ADMIN), Nome, CPF, Setor, Cargo, Data Início, Status, Ações | CPF formatado, data formatada, badge de status colorido | ✅ (verificar coluna CPF e Data Início) | — |
| Paginação | DataTables client-side, pageLength 25 | ✅ (paginate 20 server-side) | — |
| Botão "Novo Colaborador" (oculto p/ GESTOR) | Link para `colaborador_add.php`; escondido para GESTOR | ⚠️ Existe, mas sem ocultar por role | Ocultar conforme permissão |
| Menu Ações (Visualizar / Editar) | Dropdown; Editar oculto p/ GESTOR | ✅/⚠️ | Ocultar Editar para GESTOR |

---

## 2. pages/colaborador_add.php  e  3. pages/colaborador_edit.php

Campos do formulário antigo (todos): empresa_id, setor_id, cargo_id, nivel_hierarquico_id, lider_id, foto (upload+preview), nome_completo, cpf, rg, data_nascimento, telefone, email_pessoal, data_inicio, tipo_contrato (PJ/CLT/Estágio/Terceirizado), status, salario (máscara `#.##0,00`), cnpj (só PJ, com botão Sincronizar), cep, logradouro, numero, complemento, bairro, cidade_endereco, estado_endereco (UF, máscara AA), pix, banco, agencia, conta, tipo_conta (corrente/poupança), senha (≥6, opcional), observacoes.

| Funcionalidade | Antigo (comportamento) | Status no novo | Como implementar |
|---|---|---|---|
| Upload de foto + preview JS | `<input file>` com preview via FileReader; aceita JPG/PNG/GIF/WEBP até 5MB; no edit deleta foto antiga | ✅ (Alpine `setFoto`, preview; `image|max:2048` = 2MB) | Aumentar limite p/ 5MB se quiser paridade; novo grava em `fotos` (public) |
| Campo Senha de acesso (opcional, ≥6) | Cria/atualiza `senha_hash` no colaborador; gera acesso | ✅ (campo password+confirmation; `sincronizarAcesso` cria/atualiza `Usuario`) | Novo é até mais completo (cria conta users com role) |
| **CNPJ + botão "Sincronizar"** | Botão chama `api/buscar_cnpj.php?cnpj=`; preenche cep, logradouro, numero, complemento, bairro, cidade, estado, telefone, email a partir da Receita; aparece só quando CNPJ tem 14 dígitos e contrato=PJ | ⚠️ Endpoint `GET /api/cnpj/{cnpj}` (CnpjApiController via ReceitaWS) **já existe**, mas **não há botão Sincronizar na view nem JS que preencha os campos** | Adicionar botão (só PJ, 14 dígitos) que consome `/api/cnpj/{cnpj}` e popula endereço/telefone/email |
| **Carregamento dinâmico de Setor/Cargo por Empresa (AJAX)** | Ao trocar empresa, faz `fetch` em `api/get_setores.php` e `api/get_cargos.php` | ⚠️ Endpoints `GET /api/setores` e `/api/cargos` **existem**, mas o form usa selects estáticos (não os consome) | Ligar os selects aos endpoints via Alpine/JS ao mudar empresa |
| **Carregamento dinâmico de Líderes (AJAX)** | `fetch` em `api/get_lideres.php?empresa_id&setor_id&nivel_hierarquico_id&excluir_id`; filtra por empresa/setor/nível e exclui o próprio | ⚠️ Endpoint `GET /api/lideres` (`ColaboradorApiController@lideres`) existe, mas não é usado e precisa suportar filtro por setor/nível e `excluir_id` | Ligar select de líder ao endpoint + garantir filtros e exclusão do próprio |
| Validação "não pode ser líder de si mesmo" | Edit valida `lider_id == id` | ⚠️ `edit()` já remove o próprio da lista; mas não há validação explícita no `update` | Adicionar regra `lider_id != colaborador->id` no update |
| Máscaras (salário, cnpj, cep, UF) | jQuery mask | ⚠️ Não confirmado no form Tailwind | Aplicar máscaras (Alpine/JS) p/ salário/CNPJ/CEP |
| Email de boas-vindas ao criar | Se `email_pessoal` preenchido, chama `enviar_email_novo_colaborador()` | ❌ Nenhum envio de email no sistema novo (sem `Mail::`) | Criar Mailable + template e disparar no `store` |
| Campos extras do novo | — | ✅ Novo tem campos a mais: pis, ctps, matricula, sexo, estado_civil, email_login, celular, carga_horaria | Ganho do novo |
| Mapeamento `data_inicio`→`data_admissao`, `tipo_contrato`←`regime_trabalho` | — | ⚠️ Nomes diferentes (antigo `data_inicio`/`estado_endereco`/`cidade_endereco`; novo `data_admissao`/`estado`/`cidade`) | Garantir migração de dados/labels |

---

## 4. pages/colaborador_view.php (Visualização)

Antigo tem 4 abas: **Informações Pessoais**, **Informações Profissionais** (dados bancários/financeiros), **Bônus/Pagamentos** (com badge de contagem; oculta p/ COLABORADOR), **Ocorrências** (badge de contagem).

| Funcionalidade | Antigo (comportamento) | Status no novo | Como implementar |
|---|---|---|---|
| Aba Informações Pessoais | Nome, CPF, CNPJ (se PJ), RG, nascimento, telefone, email | ✅ (show exibe dados pessoais, CNPJ) | — |
| Aba Profissional + Dados Bancários | Setor, cargo, nível, líder, data início, tipo contrato, salário, status; PIX/banco/agência/conta/tipo | ✅ (show tem bloco bancário condicional) | — |
| Aba Ocorrências (lista + contagem) | Tabela de ocorrências do colaborador; botão Nova Ocorrência | ✅ | — |
| **Aba Bônus/Pagamentos + gestão CRUD** | Lista bônus ativos do colaborador; botão "Adicionar Bônus" abre modal; editar/excluir cada bônus via AJAX (ver §APIs). Mostra tipo, valor, data início, data fim, observações | ⚠️ Não há aba/gestão de bônus na view show (mostra ocorrências/HE/promoções/dependentes/formação em cards, sem tabs). Existe API `/api/bonus` (show/store/destroy) mas **quebrada** (model `Bonus` inexistente, tabela `tipo_bonus` errada, campos divergentes, sem `update`) e não integrada | Corrigir `BonusApiController` (usar `ColaboradorBonus`, tabela `tipos_bonus`, campos do schema), adicionar `update`, criar seção Bônus no show com modal Adicionar/Editar/Excluir. **Bloqueador**: schema de bônus (datas) abaixo |
| Botões Editar/Voltar com regras de role | Editar oculto p/ COLABORADOR e GESTOR | ⚠️ Verificar regras de role no show | Aplicar gates |

---

## 5. pages/promocoes.php

| Funcionalidade | Antigo (comportamento) | Status no novo | Como implementar |
|---|---|---|---|
| Registrar promoção + atualizar salário | Insere em `promocoes` e faz `UPDATE colaboradores SET salario` | ✅ (transação: cria Promocao e atualiza salário/cargo) | — |
| Auto-carregar salário atual no modal | `data-salario` no option; preenche "Salário Anterior" (readonly) ao escolher colaborador | ⚠️ Novo captura `salario_anterior`/`novo_salario`, mas confirmar auto-preenchimento do atual na view create | Adicionar JS que preenche salário anterior ao selecionar colaborador |
| Captura de Cargo novo/anterior | Antigo NÃO troca cargo (só salário+motivo) | ✅ Novo é superior: registra `cargo_anterior_id`/`cargo_novo_id` e troca cargo | Ganho do novo |
| Campo Motivo (obrigatório) + Observações | Motivo required; observacoes opcional | ⚠️ Novo: `motivo` nullable; não há campo `observacoes` separado | Tornar motivo obrigatório se desejar paridade |
| **Email de promoção** | `enviar_email_nova_promocao($promocao_id)` | ❌ Sem envio de email | Criar Mailable de promoção e disparar no `store` |
| Excluir promoção | Antigo: sem exclusão | ✅ Novo tem `destroy` | Ganho do novo |

---

## 6. pages/horas_extras.php

**Fórmula de cálculo (antigo, PHP):**
`valor_hora = salario / 220` ; `percentual = empresa.percentual_hora_extra ?? 50.00` ; `valor_hora_extra = valor_hora * (1 + percentual/100)` ; `valor_total = valor_hora_extra * quantidade_horas`. Persiste `valor_hora`, `percentual_adicional`, `valor_total`.

| Funcionalidade | Antigo (comportamento) | Status no novo | Como implementar |
|---|---|---|---|
| Cálculo do valor da hora extra | Fórmula acima (salário/220, +percentual da empresa) | ✅ Idêntica: `round(($salario/220)*(1+$percentual/100)*$horas, 2)` | — |
| Modal de cadastro | Colaborador, data, quantidade_horas, observações | ✅ (create) | Novo tem campos `motivo`/`observacao` e `status` |
| **Preview "Valor Total Calculado" em tempo real** | Card que recalcula ao digitar (mostra salário, valor hora, %, valor hora extra, total) com `colaboradoresData` e `empresasPercentual` embutidos | ⚠️ Verificar se a view create tem o box de cálculo ao vivo (não confirmado) | Implementar preview JS/Alpine com salário e percentual da empresa |
| Filtros (empresa, colaborador, status, datas) | Antigo não tinha filtros (só busca DataTables) | ✅ Novo é superior (filtros server-side) | Ganho do novo |
| Status/Aprovação de hora extra | Antigo NÃO tem status (toda HE conta) | ✅/⚠️ Novo tem `status` (pendente/aprovado) e `aprovado` | **Atenção**: muda regra do fechamento (ver §7) |
| Excluir hora extra | SweetAlert confirm → POST delete | ✅ (`destroy`) | — |
| Colunas tabela: ID, Colaborador, Empresa, Data, Quantidade, Valor Hora, % Adicional, Valor Total | — | ✅ (verificar exibição de valor_hora e %) | — |

---

## 7. pages/fechamento_pagamentos.php  (NÚCLEO DE NEGÓCIO — maiores gaps)

**Fluxo e fórmulas do antigo:**
- **Criar fechamento** (`criar_fechamento`): seleciona empresa, mês (`<input month>`) e **marca colaboradores específicos via checkboxes** (`get_colaboradores.php?...&com_salario=1`). Bloqueia se já existe fechamento p/ empresa+mês. Para cada colaborador:
  - `data_inicio = primeiro dia do mês`, `data_fim = date('Y-m-t')`.
  - Horas extras: `SUM(quantidade_horas)`, `SUM(valor_total)` em `horas_extras` no período (**sem filtro de status — todas contam**).
  - Bônus ativos no período (lógica de sobreposição de datas): permanente (ambas datas NULL) OU `(data_inicio NULL OU <= data_fim_periodo) AND (data_fim NULL OU >= data_inicio_periodo)`. Copia cada bônus para `fechamentos_pagamento_bonus`.
  - `valor_total = salario_base + valor_horas_extras + total_bonus`. Insere item; soma totais.
- **Editar item** (`atualizar_item`): edita por colaborador `horas_extras`, `valor_horas_extras`, **`descontos`**, **`adicionais`** e **lista de bônus editável** (substitui os bônus do colaborador no fechamento). `valor_total = salario_base + valor_horas_extras + total_bonus - descontos + adicionais`. Recalcula total do fechamento.
- **Fechar** (`fechar`): status→`fechado` e **envia email a cada colaborador** (`enviar_email_fechamento_pagamento`).
- **Excluir** (`delete`): com checagem de permissão por empresa; cascade nos itens.
- View detalhe: colunas Colaborador, Salário Base, Horas Extras, **Valor H.E.**, **Bônus** (link p/ modal de detalhes), **Descontos**, **Adicionais**, Total + botão Editar por linha (só status aberto). Modal "Detalhes dos Bônus" por colaborador.

| Funcionalidade | Antigo (comportamento) | Status no novo | Como implementar |
|---|---|---|---|
| Seleção de colaboradores específicos (checkboxes) ao criar | Marca quais colaboradores entram no fechamento (com salário) | ❌ Novo `fechar()` **puxa automaticamente TODOS os colaboradores ATIVOS da empresa** — não há seleção | Adicionar etapa de seleção (checkboxes via `get_colaboradores`) e gravar somente os escolhidos |
| Bloqueio de fechamento duplicado (empresa+mês) | `SELECT ... WHERE empresa_id AND mes_referencia` | ✅ (verifica empresa+mes+ano) | — |
| Cálculo de horas extras no período | `SUM(valor_total)` de TODAS as HE do mês | ⚠️ Novo soma só HE com `status='APROVADO'` e usa `whereMonth/whereYear` em `data` | Decidir regra: se quiser paridade exata, remover filtro de status; senão documentar mudança (aprovação) |
| Cálculo de bônus por período (sobreposição de datas) | Bônus ativos conforme janela data_inicio/data_fim vs período | ❌ Novo soma `Bonus where ativo=true` e filtra por `whereMonth(created_at)` — **lógica e schema divergentes** (bônus não tem período; `created_at` não representa vigência) | Reescrever: usar `colaboradores_bonus` com `data_inicio/data_fim` (adicionar colunas) e replicar a janela de sobreposição |
| **Coluna/campo Descontos por item** | Editável; subtrai do total | ❌ Tabela `fechamentos_pagamento_itens` **não tem coluna `descontos`** (model: salario_base, total_horas_extras, total_bonus, total) | Migration add `descontos`; incluir na fórmula e na edição |
| **Coluna/campo Adicionais por item** | Editável; soma ao total | ❌ Sem coluna `adicionais` | Migration add `adicionais`; incluir na fórmula e edição |
| **Edição manual de item** (HE, valor HE, descontos, adicionais, bônus) via modal | Modal "Editar Item" recalcula `salario_base + valorHE + bonus - descontos + adicionais` | ❌ View show só tem botão "Fechar Competência"; **sem edição por item** | Criar ação `atualizarItem` + modal, com recálculo idêntico |
| **Bônus editáveis no item** (add/remover/trocar) | Substitui bônus do colaborador no fechamento (DELETE+INSERT) | ❌ Não existe | Implementar gestão de bônus do item (depende do schema de bônus) |
| Modal "Detalhes dos Bônus" por colaborador | Mostra tipo, valor, datas, observações | ❌ Não existe | Implementar modal + endpoint (equivalente a `get_bonus_fechamento.php`) |
| **Email ao fechar** (por colaborador) | `enviar_email_fechamento_pagamento` para cada item | ❌ Sem email | Mailable de fechamento + envio no `fechar()` |
| Excluir fechamento (com permissão e cascade) | SweetAlert + POST; checa empresa | ✅ (`destroy`, só status ABERTO) | — |
| Status (aberto/fechado/pago) | 3 estados | ⚠️ Novo: ABERTO/FECHADO (sem "pago") | Adicionar estado "PAGO" se necessário |
| Mês de referência | `mes_referencia` (date `YYYY-MM`) | ⚠️ Novo usa `mes`+`ano` separados | Apenas formato diferente — OK |
| Recálculo de totais ao editar | `SUM(valor_total)` dos itens | ❌ (não há edição) | Implementar junto com edição de item |
| Schema de bônus do fechamento | `fechamentos_pagamento_bonus(fechamento_pagamento_id, colaborador_id, tipo_bonus_id, valor, observacoes)` | ⚠️ Novo: `(fechamento_item_id, colaborador_bonus_id, valor)` — sem `observacoes`, referencia bônus em vez de tipo | Ajustar conforme nova abordagem de edição |

---

## 8. pages/tipos_bonus.php

| Funcionalidade | Antigo (comportamento) | Status no novo | Como implementar |
|---|---|---|---|
| CRUD (add/edit/delete) via modal | Nome*, descrição, status (ativo/inativo) | ✅ (controller resource completo; create/edit em páginas) | — |
| Proteção ao excluir (em uso) | Bloqueia delete se houver `colaboradores_bonus` com o tipo | ❌ `destroy` apaga direto (e migration tem cascade → apagaria bônus em uso) | Adicionar verificação de uso antes de excluir (ou `restrictOnDelete`) |
| Campos extras | Antigo: só nome/descrição/status | ✅ Novo tem `empresa_id`, `tipo_calculo`, `percentual`, `fixo` | Ganho do novo |
| Busca/filtro | DataTables search | ✅ (search + filtro empresa) | — |

---

## APIs AJAX

| API antiga | Função | Status no novo | Como implementar |
|---|---|---|---|
| `api/get_colaboradores.php` | JSON de colaboradores por empresa/status/com_salario; checa `can_access_empresa` | ⚠️ Existe `GET /api/colaboradores` (`ColaboradorApiController@index`); validar params `status`/`com_salario` e escopo; ainda não usado na seleção do fechamento | Reutilizar p/ checkboxes do fechamento |
| `api/salvar_bonus_colaborador.php` | add/edit bônus do colaborador (colaborador_id, tipo_bonus_id, valor, data_inicio, data_fim, observacoes); permissão ADMIN/RH | ⚠️ `POST /api/bonus` existe mas **quebrado**: model `Bonus` inexistente, valida `tipo_bonus` (tabela errada), campos `descricao/registrado_por/ativo` divergentes; **sem ação edit/update**. Schema `colaboradores_bonus` **sem `data_inicio/data_fim/observacoes`** e com unique (colaborador+tipo) que impede múltiplos do mesmo tipo | Corrigir controller (usar `ColaboradorBonus`, tabela `tipos_bonus`); add `update`; **migration** add `data_inicio`, `data_fim`, `observacoes` e remover o unique |
| `api/get_bonus_colaborador.php` | Lista bônus vigentes (data_fim NULL ou >= hoje) | ⚠️ `GET /api/bonus/{colaborador}` existe (lista todos), mas sem filtro de vigência por data | Adicionar filtro de vigência; usar na aba Bônus do show |
| `api/deletar_bonus_colaborador.php` | Exclui bônus (ADMIN/RH) | ⚠️ `DELETE /api/bonus/{bonus}` existe (tipa `Bonus` inexistente) | Corrigir model p/ `ColaboradorBonus` |
| `api/get_bonus_fechamento.php` | Bônus salvos de um colaborador num fechamento | ❌ Sem endpoint | Criar p/ o modal de edição de item |
| `api/buscar_cnpj.php` | Lookup de CNPJ p/ sincronizar endereço | ⚠️ `GET /api/cnpj/{cnpj}` existe (ReceitaWS), mas não está ligado ao botão/form | Adicionar botão Sincronizar e JS de preenchimento |

---

## TOP GAPS PRIORITÁRIOS

1. **Fechamento — edição manual por item (Descontos + Adicionais + HE + Bônus)**: o novo não permite editar itens nem possui colunas `descontos`/`adicionais`. Fórmula antiga: `salario_base + valor_horas_extras + total_bonus - descontos + adicionais`. Requer migration nas colunas + ação `atualizarItem` + modal. **(❌ crítico)**

2. **Fechamento — seleção de colaboradores e regra de bônus/HE por período**: o novo puxa todos os ATIVOS automaticamente, conta só HE `APROVADO`, e calcula bônus por `ativo + whereMonth(created_at)` (errado). O antigo seleciona colaboradores via checkbox e aplica janela de sobreposição de datas dos bônus. **(❌ crítico)**

3. **Gestão de Bônus por colaborador (CRUD) + schema**: inexistente no novo. `colaboradores_bonus` não tem `data_inicio/data_fim/observacoes` e tem unique (colaborador+tipo) que impede múltiplos bônus. Necessário migration + `ColaboradorBonusController` + aba Bônus no show com modal. Bloqueia também o cálculo correto do fechamento. **(❌ crítico)**

4. **CNPJ "Sincronizar" (lookup automático de endereço)**: campo CNPJ existe, mas não há botão nem endpoint de consulta à Receita/BrasilAPI que preenche endereço/telefone/email. **(❌)**

5. **Emails transacionais**: nenhum envio no novo. Faltam: boas-vindas (novo colaborador), promoção, e fechamento de pagamento (por colaborador). **(❌)**

6. **Carregamento dinâmico (AJAX) de Setor/Cargo/Líder por Empresa** no formulário de colaborador: o novo usa selects estáticos; falta filtragem encadeada e endpoint de líderes (com exclusão do próprio e filtro por setor/nível). **(⚠️)**

7. **Preview "Valor Total Calculado" em tempo real** no cadastro de hora extra (cálculo idêntico ao backend exibido ao digitar). **(⚠️)**

8. **Escopo por papel na listagem de colaboradores** (RH→empresa, GESTOR→setor, COLABORADOR→próprio) e ocultação de botões por role; filtro por Setor na listagem. **(⚠️)**

9. **tipos_bonus — proteção ao excluir tipo em uso**: o novo apaga direto com cascade, podendo remover bônus vinculados. **(❌ risco de dado)**

10. **BUGS de código no novo (corrigir antes de integrar)**: `BonusApiController` e `FechamentoPagamentoController` importam `App\Models\Bonus` (inexistente — deveria ser `ColaboradorBonus`); `BonusApiController@store` valida tabela `tipo_bonus` (correto: `tipos_bonus`) e grava campos fora do schema (`descricao/registrado_por/ativo`). Esses endpoints quebram em runtime. **(❌ crítico)**

11. **APIs existem mas não estão integradas às telas**: `/api/setores`, `/api/cargos`, `/api/lideres`, `/api/cnpj`, `/api/bonus` estão definidas, porém os formulários/views não as consomem (selects estáticos, sem botão Sincronizar, sem aba de bônus). Esforço é principalmente de front-end + correção dos bugs acima. **(⚠️)**
# Auditoria de Paridade — Módulo de Ocorrências (C)

Sistema ANTIGO: `/home/user/rh-privus/` (PHP puro)
Sistema NOVO: `/home/user/tallents/` (Laravel)
Data: 2026-06-13

Legenda: ✅ existe | ⚠️ parcial | ❌ falta

Observação geral: o NOVO está em muitos pontos MAIS completo que o antigo (anexos,
comentários, histórico, edição, gravidade). O foco abaixo é o que do antigo ficou
faltando/parcial.

---

## 1. pages/ocorrencias_add.php → ocorrencias/create.blade.php + OcorrenciaController@store

| Funcionalidade | Antigo | Status no novo | Como implementar |
|---|---|---|---|
| Select de colaborador | Sim, obrigatório | ✅ | — |
| Tipo de ocorrência agrupado por categoria (`<optgroup>`) | Tipos vêm do banco ordenados por categoria e renderizados em optgroups (Pontualidade, Comportamento, Desempenho, Outros) | ⚠️ | O novo lista tipos num select plano sem optgroup por categoria. Agrupar por `categoria` no Blade (ATRASO/FALTA/PONTO/OUTROS) para paridade visual. |
| Data da ocorrência (default hoje) | Sim | ✅ | — |
| Hora da ocorrência | Sim; JS preenche hora atual do cliente automaticamente | ⚠️ | Campo existe, mas o NOVO não pré-preenche a hora atual via JS. Adicionar script que seta `hora_ocorrencia` = hora local ao carregar. |
| Campo condicional Tempo de atraso (data-permite-tempo) | Mostra/oculta via JS conforme flag do tipo; vira obrigatório quando visível | ⚠️ | Show/hide existe via Alpine (`data-atraso`). Mas no antigo o campo fica REQUIRED quando visível (valida no submit + servidor). No novo não há `required` condicional nem validação server-side de obrigatoriedade. Adicionar regra: se tipo `permite_tempo_atraso`, exigir `tempo_atraso_minutos`. |
| Campo condicional Tipo de ponto (data-permite-ponto) | Show/hide + obrigatório quando visível | ⚠️ | Idem acima: exigir `tipo_ponto` quando `permite_tipo_ponto`. Atenção: valores diferem — antigo usa `entrada/almoco/cafe/saida` (4 opções), novo usa `ENTRADA/SAIDA/INTERVALO` (3). Avaliar mapear `almoco`+`cafe`→`INTERVALO` ou ampliar enum. |
| Descrição (textarea) | Sim | ✅ (mapeada para `observacao`/`descricao`) | — |
| Campo `tipo` (texto legado) p/ compatibilidade | Mantém string do tipo p/ compat. com registros antigos | ❌ (não migrado) | Provavelmente desnecessário no novo; só relevante se a migração de dados depender da coluna `tipo`. Ignorável. |
| Validação JS de tempo/ponto com SweetAlert | Sim | ⚠️ | Novo usa validação Laravel + HTML5. Sem alerta SweetAlert, mas funcionalmente equivalente. |
| Envio de e-mail ao colaborador (`enviar_email_ocorrencia`) | SEMPRE dispara e-mail ao colaborador após salvar, usando template `ocorrencia` com variáveis (nome, tipo, data, hora, tempo de atraso formatado h/min, descrição, registrado por, empresa, setor, cargo) | ❌ | **GAP CRÍTICO.** O novo só grava o booleano `notificar_colaborador`; NENHUM e-mail é enviado (nenhum Mail::/Notification no app). Criar Mailable/Notification `OcorrenciaRegistrada`, disparar no `store()` quando `notificar_colaborador` (ou sempre, como o antigo). Reaproveitar `ConfiguracaoEmail`. Formatar tempo de atraso em "Xh Ymin". |
| Permissão por papel ao lançar | COLABORADOR bloqueado; `can_access_colaborador()` valida escopo (RH=empresa, GESTOR=setor) | ❌ | O novo não restringe quais colaboradores aparecem nem valida escopo. `create()` lista TODOS colaboradores ATIVOS sem filtrar por empresa/setor do usuário logado. Implementar scoping por papel. |
| Gravidade | Não existe no antigo | ✅ (novo a mais) | — |

---

## 2. pages/ocorrencias_list.php → ocorrencias/index.blade.php + OcorrenciaController@index

| Funcionalidade | Antigo | Status no novo | Como implementar |
|---|---|---|---|
| Botão Nova Ocorrência | Sim | ✅ | — |
| Filtro por colaborador | Select de colaboradores escopados por papel | ⚠️ | **BUG de wiring:** a view envia `name="busca"` (texto livre) e `name="tipo_id"`, mas o controller lê `colaborador_id` e `tipo_ocorrencia_id`. Os filtros da listagem NÃO funcionam. Alinhar nomes view↔controller e adicionar busca textual por nome de colaborador. |
| Filtro por tipo | Select de tipos | ⚠️ | Mesmo bug (`tipo_id` vs `tipo_ocorrencia_id`). Corrigir. |
| Filtro por data início/fim | Sim | ✅ (controller lê `data_inicio`/`data_fim`; view envia iguais) | — |
| Botão limpar filtros | Sim | ✅ | — |
| Coluna Data | `formatar_data` | ✅ | — |
| Coluna Colaborador (link p/ ficha) | Link p/ colaborador_view | ⚠️ | Mostra nome mas sem link para a ficha do colaborador. Adicionar link para `colaboradores.show`. |
| Coluna Tipo com badge colorido por gravidade do tipo | Badge: elogio=success, advertência=danger, demais=warning | ⚠️ | Novo mostra badge laranja fixo. Colorir por categoria/gravidade. |
| Coluna Descrição truncada (100 chars) | substr 100 + "..." | ✅ (usa `truncate`) | — |
| Coluna Registrado por | Sim | ❌ | A listagem nova NÃO mostra quem registrou (`registradoPor`). Adicionar coluna. |
| Coluna Data Registro (created_at) | Sim, com hora | ❌ | Não exibida no novo. Adicionar. |
| Paginação | DataTables (client-side, sem paginação server) | ✅ (melhor: paginate(20) server-side) | — |
| Ações editar/excluir na linha | Antigo não tinha (só visualização) | ✅ (novo a mais) | — |
| Escopo por papel (RH=empresa, GESTOR=setor) | Sim, no WHERE | ❌ | Novo não aplica escopo; lista todas as ocorrências independente do papel. Implementar. |

---

## 3. pages/tipos_ocorrencias.php → tipos-ocorrencias/* + TipoOcorrenciaController

| Funcionalidade | Antigo | Status no novo | Como implementar |
|---|---|---|---|
| Listar tipos | Sim | ✅ | — |
| Busca por nome | JS DataTable search | ✅ (controller `search` por nome) | — |
| Campo Nome | Obrigatório | ✅ | — |
| Campo **Código** único | Obrigatório, validado único (add e edit excluindo o próprio) | ❌ | **GAP.** O novo NÃO tem campo `codigo` (não existe no controller, model nem views). O antigo usa código para mapear tipos legados e garantir unicidade. Se a integração/migração depende do código, adicionar coluna `codigo` + validação `unique`. Avaliar necessidade. |
| Campo Categoria | pontualidade/comportamento/desempenho/outros | ⚠️ | Existe, mas valores divergem: novo usa `ATRASO/FALTA/PONTO/OUTROS`. Alinhar taxonomia (impacta agrupamento do create e relatório). |
| Flag Permite tempo de atraso | checkbox | ✅ | — |
| Flag Permite tipo de ponto | checkbox | ✅ | — |
| Status ativo/inativo | Sim | ✅ (campo `ativo` boolean) | — |
| Criar via modal | Modal Bootstrap inline | ⚠️ | Novo usa páginas create/edit dedicadas (UX diferente, equivalente). OK. |
| Editar (preenche modal via JS) | Sim | ✅ (página edit) | — |
| **Excluir com proteção** (bloqueia se há ocorrências usando o tipo) | Conta ocorrências; se >0, impede exclusão com mensagem do total | ❌ | **GAP.** O `destroy()` do novo apaga direto sem checar dependências. Adicionar verificação: `if ($tipo->ocorrencias()->exists())` → impedir e avisar. A relação `ocorrencias()` já existe no model. |
| Confirmação de exclusão | SweetAlert | ⚠️ | Provável `confirm()` simples; verificar na index. Funcional. |
| Campo Empresa (multi-empresa) | Não existe no antigo | ✅ (novo a mais) | — |
| Gravidade padrão do tipo | Não existe no antigo | ✅ (novo a mais) | — |

---

## 4. pages/relatorio_ocorrencias.php → ocorrencias/relatorio.blade.php + OcorrenciaController@relatorio

| Funcionalidade | Antigo | Status no novo | Como implementar |
|---|---|---|---|
| Filtro Empresa (ADMIN) | Sim | ✅ | — |
| Filtro **Setor** | Sim | ❌ | O novo não tem filtro por setor. Adicionar select de setor + `whereHas('colaborador', setor_id)`. |
| Filtro Colaborador | Sim | ⚠️ | Controller suporta `colaborador_id`, mas a VIEW do relatório não renderiza o select de colaborador (só empresa/tipo/datas). Adicionar select de colaborador na view. |
| Filtro Tipo | Sim | ✅ | — |
| Filtro Data início/fim (default mês corrente) | Default `Y-m-01` a `Y-m-t` | ⚠️ | Novo tem os campos mas SEM default de mês corrente. Adicionar defaults. |
| Tabela detalhada (Data, Empresa, Setor, Colaborador, CPF, Tipo, Descrição, Registrado por) | Sim, linha a linha | ❌ | O novo relatório só mostra AGREGADOS (cards + por tipo + top colaboradores). NÃO há a tabela detalhada linha a linha com CPF/setor/registrado por. Adicionar tabela detalhada (ou usar a listagem index para isso). |
| **Exportação CSV** | Gera CSV com BOM UTF-8, separador `;`, cabeçalho e todas as linhas (Data, Empresa, Setor, Colaborador, CPF, Tipo, Descrição, Registrado por, Data Registro) | ❌ | **GAP CRÍTICO.** Não existe qualquer exportação no novo. Implementar rota/método de export CSV (e idealmente Excel). Reproduzir BOM `\xEF\xBB\xBF`, separador `;`, e as mesmas colunas, respeitando filtros aplicados. |
| Exportação PDF / impressão | (Antigo só tinha CSV) | ❌ (nenhuma) | Antigo não tinha PDF; mas a tarefa pede conferir — não há export PDF/imprimir em nenhum dos dois. Opcional implementar. |
| Agregação por gravidade | Não existe no antigo | ✅ (novo a mais) | — |
| Agregação por tipo (contagem) | Não existe no antigo (antigo era só tabela) | ✅ (novo a mais) | — |
| Agregação top colaboradores | Não existe no antigo | ✅ (novo a mais) | — |
| Total de ocorrências | Não no antigo | ✅ (novo a mais) | — |
| **Somatório de tempo de atraso** | Não no antigo | ✅ (novo a mais: `sum('tempo_atraso_minutos')`) | — |
| **Agrupamento por mês** | Não existe no antigo | ❌ (nem no novo) | A tarefa cita "por mês". Nenhum dos dois agrupa por mês. Implementar agregação `groupByRaw(YEAR/MONTH)` para gráfico de evolução mensal (melhoria). |
| Gráficos | Nenhum dos dois tem gráfico | ❌ | Opcional: adicionar gráfico (barras por tipo, linha por mês) no relatório novo. |
| Tempo total de atraso formatado h/min | Antigo formatava no e-mail | ⚠️ | Relatório novo mostra total em minutos crus. Formatar como "Xh Ymin" para legibilidade. |
| Escopo por papel no relatório | RH=empresa, GESTOR=setor aplicados no WHERE | ❌ | Novo não escopa por papel. Implementar. |

---

## TOP GAPS PRIORITÁRIOS

1. **[CRÍTICO] Notificação por e-mail ao colaborador** — o antigo SEMPRE enviava e-mail
   (`enviar_email_ocorrencia`) com template e variáveis (incl. tempo de atraso formatado).
   O novo apenas grava o flag `notificar_colaborador` e NUNCA envia nada. Criar
   Mailable/Notification e disparar no `store()` (e talvez `update()`).

2. **[CRÍTICO] Exportação CSV do relatório** — inexistente no novo. O antigo exporta CSV
   (BOM UTF-8, `;`, colunas Data/Empresa/Setor/Colaborador/CPF/Tipo/Descrição/Registrado
   por/Data Registro), respeitando filtros. Implementar export (CSV e idealmente Excel).

3. **[CRÍTICO] Filtros da listagem quebrados** — `index.blade.php` envia `busca`/`tipo_id`
   mas o controller lê `colaborador_id`/`tipo_ocorrencia_id`. Os filtros não têm efeito.
   Alinhar nomes e adicionar busca textual por colaborador.

4. **[ALTO] Escopo de segurança por papel ausente** — em add, list e relatório o antigo
   restringia por papel (COLABORADOR bloqueado; RH=empresa; GESTOR=setor) e
   `can_access_colaborador()`. O novo expõe todos os dados/colaboradores a qualquer usuário.
   Implementar scoping por papel em `index`, `create`, `relatorio` e validação no `store`.

5. **[ALTO] Exclusão de tipo sem proteção** — `TipoOcorrenciaController@destroy` apaga sem
   checar dependências. O antigo bloqueia se há ocorrências usando o tipo. Usar
   `$tipo->ocorrencias()->exists()`.

6. **[ALTO] Relatório sem tabela detalhada nem filtros de setor/colaborador** — o relatório
   novo só tem agregados. Falta: tabela linha a linha (com CPF, setor, registrado por),
   filtro por setor, select de colaborador na view, e defaults de período (mês corrente).

7. **[MÉDIO] Obrigatoriedade condicional de tempo de atraso / tipo de ponto** — no antigo,
   quando o tipo permite, o campo vira obrigatório (JS + implícito). No novo não há
   validação server-side condicional. Adicionar regras condicionais no `validate()`.

8. **[MÉDIO] Campo `codigo` do tipo de ocorrência ausente** — antigo tem código único usado
   para mapear tipos legados. Avaliar se a migração precisa; se sim, adicionar coluna +
   validação `unique`.

9. **[MÉDIO] Taxonomia de categoria/tipo de ponto divergente** — categorias
   (pontualidade/comportamento/desempenho/outros vs ATRASO/FALTA/PONTO/OUTROS) e tipo de
   ponto (entrada/almoco/cafe/saida vs ENTRADA/SAIDA/INTERVALO) não batem. Padronizar para
   não quebrar agrupamentos e migração de dados.

10. **[BAIXO] Detalhes de listagem** — faltam colunas "Registrado por" e "Data Registro",
    link do colaborador para a ficha, badge colorido por categoria, pré-preenchimento da
    hora atual no create, e agrupamento/gráfico por mês no relatório (melhoria nova).
# Auditoria de Paridade — Sistema (Usuários, Minha Conta, E-mail, Templates, OneSignal, Push, Permissões)

Comparação: ANTIGO (PHP puro, `/home/user/rh-privus/`) x NOVO (Laravel, `/home/user/tallents/`).
Data: 2026-06-13.

Legenda: ✅ existe | ⚠️ parcial | ❌ falta

---

## 1. pages/usuarios.php — Gestão de Usuários

Novo: `UsuarioController` + `resources/views/usuarios/{index,create,edit,show,_form}.blade.php`, rota `Route::resource('usuarios', ...)`.

| Funcionalidade | Antigo | Status no novo | Como implementar |
|---|---|---|---|
| Listar usuários (nome, email, perfil, empresas, último login) | Tabela com badges de perfil, empresas concatenadas, último login | ✅ | Index existe; mostra last_login. |
| Criar / editar usuário (nome, email, senha, perfil) | Modal único add/edit | ✅ | create/edit + _form cobrem. |
| Papel/role (ADMIN, RH, GESTOR, COLABORADOR) | Select obrigatório; normaliza role | ✅ | _form tem select "perfil" mapeado p/ role. |
| Vínculo a múltiplas empresas (N:N `usuarios_empresas`) | Checkboxes de empresas; regra: não-ADMIN exige ≥1 empresa | ⚠️ | Checkboxes existem e `sync()` funciona, mas **falta a regra de validação "se não-ADMIN, ≥1 empresa obrigatória"** (server e client). Adicionar `required_unless:perfil,admin` ou validação custom no `store/update`. |
| Vínculo a colaborador (opcional) | Select colaborador | ✅ | Campo `colaborador_id` presente. |
| **Setor (obrigatório p/ GESTOR)** | Select setor carregado via AJAX por empresa; obrigatório se role=GESTOR | ❌ | _form **não tem campo setor**. Antigo: GESTOR exige `setor_id`; setor depende da empresa (endpoint `api/get_setores.php?empresa_id=`). Implementar: campo setor no _form (dependente da empresa via Alpine/fetch a um endpoint de setores por empresa), validação `required_if:perfil,gestor`, e gravar `setor_id`. |
| **Upload de foto do usuário** | `<input file>` com preview; `upload_foto_perfil('usuario')`; deleta foto antiga ao trocar | ❌ | create/edit/_form **não têm campo foto**. `UsuarioController::store/update` não tratam `foto`. Adicionar input file, validação `image|max:2048`, `->store('perfis','public')`, deletar anterior, gravar `users.foto`. |
| Status ativo/inativo | Select status (ativo/inativo) | ⚠️ | Campo `ativo` (checkbox) existe no _form e show. Equivale, mas semântica é boolean vs enum; ok. Garantir filtro/exibição na index (index mostra last_login mas confirmar coluna de status). |
| Validação email único | Verifica duplicado | ✅ | `unique:users,email`. |
| Excluir usuário (não a si mesmo) | Bloqueia auto-exclusão; SweetAlert confirm | ✅ | `destroy` bloqueia auto-exclusão. |
| Busca por nome/email | Filtro client-side (DataTables) | ✅ | `index` aceita `search`. |
| **E-mail de boas-vindas ao criar** (implícito) | `enviar_email_boas_vindas()` existe em includes/email.php | ❌ | Não é disparado em usuarios.php do antigo, mas a função existe. Ver seção E-mail. |
| Criação automática da tabela `usuarios_empresas` + migração de dados | `CREATE TABLE IF NOT EXISTS` inline | ✅ (n/a) | Tratado por migrations no Laravel. |

**Permissão (importante):** Antigo = **apenas ADMIN** (`check_permission('ADMIN')`). Novo = rota sob `papel:ADMIN,RH,GESTOR` → **RH e GESTOR também acessam a gestão de usuários**. Ver seção Permissões.

---

## 2. pages/minha_conta.php — Minha Conta

Novo: `PerfilController` + `resources/views/perfil/index.blade.php`, rotas `perfil.*`.

| Funcionalidade | Antigo | Status no novo | Como implementar |
|---|---|---|---|
| Card de perfil (foto, nome, role, empresa, setor) | Exibe foto, role, empresa, setor, último acesso | ⚠️ | Card mostra foto + nome + email. **Não mostra role/empresa/setor/último acesso.** Adicionar esses campos ao card (read-only). |
| Editar dados pessoais (nome, email) | Form aba "Dados Pessoais" | ✅ | `perfil.update`. |
| **Editar telefone (quando vinculado a colaborador)** | Campo telefone que grava em `colaboradores.telefone` | ❌ | PerfilController não trata telefone. Se `colaborador_id`, exibir campo telefone e atualizar `colaboradores.telefone`. |
| Alterar senha (senha atual + nova + confirmar, mín. 6/8) | Aba "Alterar Senha"; valida senha atual e confirmação | ✅ | `perfil.senha`; valida `senha_atual` + `Password::min(8)`. **Atenção:** view envia `current_password`, controller espera `senha_atual` → conferir mismatch de nome de campo (possível bug). |
| Upload/troca de foto | Aba "Foto de Perfil" com preview; deleta antiga | ✅ | `perfil.update` trata `foto`, deleta anterior, `store('perfis')`. |
| Excluir a própria conta | Não existe no antigo | ➕ (extra no novo) | Novo adiciona "Zona de Perigo" (não era requisito). |
| Atualiza sessão após editar | Atualiza `$_SESSION` | ✅ (n/a) | Auth do Laravel já reflete. |

**Bug potencial a corrigir:** form de senha usa `name="current_password"` mas `PerfilController::updateSenha` lê `senha_atual` → a validação falhará. Alinhar nomes.

---

## 3. pages/configuracoes_email.php — Configurações SMTP

Novo: `ConfiguracaoController` (aba E-mail) + `configuracoes/index.blade.php`, model `ConfiguracaoEmail`.

| Funcionalidade | Antigo | Status no novo | Como implementar |
|---|---|---|---|
| Host SMTP | Campo obrigatório | ✅ | `mail_host`. |
| Porta | Campo obrigatório | ✅ | `mail_port`. |
| Segurança (TLS/SSL) | Select tls/ssl | ✅ | `mail_encryption` (tls/ssl/nenhuma). |
| Usuário SMTP | Campo | ✅ | `mail_username`. |
| Senha SMTP (preserva se vazio; toggle mostrar) | Senha + botão olho | ⚠️ | Persistência só-se-preenchido OK. **Falta o toggle "mostrar senha"** (cosmético). |
| Email e Nome do remetente | from_email, from_name obrigatórios | ✅ | `mail_from_address`, `mail_from_name`. |
| **Flag "requer autenticação" (smtp_auth)** | Checkbox; campos user/senha condicionais | ❌ | Novo não tem `smtp_auth`. Adicionar campo/coluna `smtp_auth` e lógica. |
| **Modo Debug (smtp_debug)** | Checkbox | ❌ | Adicionar `smtp_debug`. |
| **Testar envio de e-mail** | Modal "email_teste" → `enviar_email()` real; mensagem sucesso/erro | ❌ | Botão "Testar Envio" existe na view **mas é decorativo (sem ação/rota)**. Implementar rota `POST configuracoes/email/testar` que envia e-mail real usando as configs salvas e retorna resultado. |
| **Aplicar config do banco ao mailer em runtime** | `enviar_email()` lê `configuracoes_email` do banco e injeta no PHPMailer | ❌ | No novo **nada usa `ConfiguracaoEmail` para enviar** (nenhum `Mail::`). É preciso um `MailConfigService`/middleware que sobrescreva `config('mail.*')` com os valores do banco antes de enviar. |
| Criação automática da tabela + seed de templates padrão | `CREATE TABLE` + 4 templates inline | ✅ (n/a) | Migrations/seeders. |

---

## 4. pages/templates_email.php (e aba Templates) — Templates de E-mail

Novo: model `App\Models\EmailTemplate` existe, **mas órfão** — sem controller, rota ou view.

| Funcionalidade | Antigo | Status no novo | Como implementar |
|---|---|---|---|
| Listar templates (nome, código, assunto, status) | Tabela | ❌ | Criar `EmailTemplateController@index` + view + rota. |
| Editar template (assunto, corpo HTML, corpo texto, descrição, ativo) | Modal com **editor TinyMCE** no corpo HTML | ❌ | Criar `edit/update`. Incluir editor rich-text (TinyMCE/Trix) p/ `corpo`. Campos: nome, assunto, corpo_html, corpo_texto, descricao, ativo. **Nota:** model novo tem `corpo` (singular) + `slug`+`variaveis`; antigo usa `codigo`+`corpo_html`+`corpo_texto`. Mapear/migrar schema. |
| Ativar/desativar template (toggle AJAX) | Switch toggle via fetch | ❌ | Endpoint `PATCH templates/{id}/toggle`. |
| Código único imutável | Campo readonly | ❌ | Equivalente a `slug`. |
| Lista de variáveis disponíveis por template | Card com `{nome_completo}`, `{empresa_nome}`, etc. (4 grupos: novo_colaborador, nova_promocao, fechamento_pagamento, ocorrencia) | ❌ | Reproduzir painel de variáveis. Variáveis por template no antigo:<br>**novo_colaborador:** {nome_completo}{empresa_nome}{cargo_nome}{setor_nome}{data_inicio}{tipo_contrato}{cpf}{email_pessoal}{telefone}<br>**nova_promocao:** {nome_completo}{data_promocao}{salario_anterior}{salario_novo}{motivo}{observacoes}{empresa_nome}<br>**fechamento_pagamento:** {nome_completo}{mes_referencia}{salario_base}{horas_extras}{valor_horas_extras}{descontos}{adicionais}{valor_total}{data_fechamento}{observacoes}<br>**ocorrencia:** {nome_completo}{tipo_ocorrencia}{data_ocorrencia}{hora_ocorrencia}{tempo_atraso}{descricao}{usuario_registro}{data_registro}{empresa_nome}{setor_nome}{cargo_nome} |
| Substituição de variáveis `{chave}` no envio (`substituir_variaveis`) | `str_replace('{chave}', valor)` | ❌ | Implementar render (str_replace ou Blade string). |
| Envio por template (`enviar_email_template($codigo, $email, $vars)`) | Busca template ativo por código, substitui vars, envia | ❌ | Criar serviço `EmailTemplateService::enviar($slug, $email, $vars)`. |
| Disparos automáticos: novo colaborador, promoção, fechamento, ocorrência | `enviar_email_novo_colaborador()`, `enviar_email_nova_promocao()`, `enviar_email_fechamento_pagamento()`, `enviar_email_ocorrencia()` (em includes/email_templates.php) | ❌ | Disparar nos respectivos controllers (ColaboradorController@store, PromocaoController@store, FechamentoPagamentoController@fechar, OcorrenciaController@store). Cada um monta o array de variáveis (ver mapeamentos acima) e chama o serviço de template. |

---

## 5. pages/configuracoes_onesignal.php — Config OneSignal

Novo: models `OnesignalConfig` + `OnesignalSubscription` + `PushSubscription` + `VapidKey` existem, **mas órfãos** — sem controller/rota/view.

| Funcionalidade | Antigo | Status no novo | Como implementar |
|---|---|---|---|
| Salvar App ID | Campo obrigatório | ❌ | Criar `OnesignalController@index/update` + view + rota. Model já tem `app_id`. |
| Salvar REST API Key | Campo obrigatório (password) | ❌ | Idem; model tem `rest_api_key` (já `$hidden`). |
| Safari Web ID | Campo opcional | ❌ | Model novo **não tem `safari_web_id`** — adicionar coluna/fillable. |
| Painel "Como obter credenciais" | Instruções estáticas | ❌ | Reproduzir bloco informativo. |
| Status da integração (configurado? nº dispositivos) | Cards de status + contagem de `onesignal_subscriptions` | ❌ | Implementar contagem via `OnesignalSubscription::count()`. |
| Lista de dispositivos registrados (player_id, usuário, colaborador, tipo, data) | Tabela últimos 10 | ❌ | Listar `OnesignalSubscription` com joins usuário/colaborador. |

---

## 6. pages/enviar_notificacao_push.php — Envio de Push

Novo: **inexistente** (sem controller/rota/view; só models órfãos).

| Funcionalidade | Antigo | Status no novo | Como implementar |
|---|---|---|---|
| Listar usuários/colaboradores com push registrado | Query agregada em `onesignal_subscriptions` (GROUP_CONCAT player_ids, nº dispositivos) | ❌ | Controller que agrega `OnesignalSubscription` por usuario_id/colaborador_id. |
| Filtros: nome, email, perfil | Form GET | ❌ | Implementar filtros. |
| Estatísticas (total usuários, dispositivos, último registro) | 3 cards | ❌ | Implementar. |
| Modal "Enviar Notificação": destinatário, título, mensagem, URL opcional | Form modal | ❌ | View + rota POST. |
| Envio individual (por usuário OU colaborador) | `enviar_push_usuario()` / `enviar_push_colaborador()` | ❌ | Serviço que chama a API REST do OneSignal (`https://onesignal.com/api/v1/notifications`) usando App ID + REST API Key, filtrando por player_ids do destinatário; URL default = dashboard/colaborador_view. |
| Envio em lote (`enviar_push_colaboradores`) | Loop sobre IDs | ❌ | Suportar múltiplos destinatários/segmentação. |
| Título + mensagem obrigatórios; ≥1 destinatário | Validação | ❌ | Validar. |
| URL de clique (relativa→absoluta) | Normaliza URL | ❌ | Implementar no serviço. |

**Permissão antiga:** ADMIN e RH (`in_array(role, ['ADMIN','RH'])`).

---

## 7. includes/auth.php + functions.php — Permissões/Papéis

| Regra (antigo) | Antigo | Status no novo | Como implementar |
|---|---|---|---|
| Papéis: ADMIN, RH, GESTOR, COLABORADOR | enum | ✅ | `users.role` + `EnsureRole` (`papel:`). |
| **usuarios / config e-mail / templates / onesignal = SÓ ADMIN** | `check_permission('ADMIN')` em cada página | ⚠️/❌ | No novo, todas estão sob `papel:ADMIN,RH,GESTOR` → **RH e GESTOR acessam indevidamente** gestão de usuários e configurações. Restringir essas rotas a `papel:ADMIN` (separar grupo de rotas administrativas-sensíveis). |
| Push = ADMIN+RH | `in_array(role,['ADMIN','RH'])` | ❌ | Ao implementar push, proteger com `papel:ADMIN,RH`. |
| `can_access_empresa()` — RH limitado às empresas vinculadas; ADMIN tudo | função | ✅ | `Usuario::canAccessEmpresa()` replica. |
| `can_access_setor()` — RH via empresa do setor; GESTOR só seu setor | função | ⚠️ | `canAccessEmpresa` existe; **não há `canAccessSetor`/`canAccessColaborador`** equivalentes. Implementar se houver telas que dependam de escopo de setor/colaborador (GESTOR). |
| `can_access_colaborador()` — ADMIN tudo; RH por empresa; GESTOR por setor; COLABORADOR só o próprio | função | ⚠️ | Verificar se controllers de colaborador aplicam esse escopo; senão implementar policy. |
| RH "tem acesso a tudo exceto ADMIN" (check_permission) | regra | ⚠️ | Parcialmente coberto pelo `papel:` por rota, mas a granularidade ADMIN-only acima precisa ser corrigida. |

---

## 8. includes/email.php — Envio SMTP (PHPMailer)

| Funcionalidade | Antigo | Status no novo | Como implementar |
|---|---|---|---|
| Envio SMTP genérico (`enviar_email`) lendo config do banco | PHPMailer + `configuracoes_email` | ❌ | Nenhum envio real de e-mail no novo (`Mail::` ausente). Criar serviço de envio que aplica `ConfiguracaoEmail` ao mailer. |
| Suporte a HTML + AltBody (texto), CC, BCC, Reply-To, anexos | parâmetros `$opcoes` | ❌ | Mailable Laravel cobre nativamente; expor essas opções. |
| E-mail de boas-vindas (`enviar_email_boas_vindas`) com senha temporária | template HTML inline | ❌ | Mailable + opção ao criar usuário. |
| E-mail de recuperação de senha (`enviar_email_recuperacao_senha`) c/ token (expira 1h) | template HTML inline + link | ⚠️ | Laravel tem reset de senha nativo; confirmar se está habilitado/rota existe. Se não, habilitar `Password::sendResetLink`. |

---

## TOP GAPS PRIORITÁRIOS

1. **Permissões erradas (CRÍTICO/segurança):** No novo, `usuarios`, `configuracoes` (e-mail) estão sob `papel:ADMIN,RH,GESTOR`. No antigo são **ADMIN-only**. RH/GESTOR conseguem gerenciar usuários e SMTP. → Restringir essas rotas a `papel:ADMIN`.

2. **Envio de e-mail não funciona (CRÍTICO):** O novo salva config SMTP mas **nunca envia e-mail** (sem `Mail::`/Mailable; config do banco não é aplicada ao mailer; botão "Testar Envio" é decorativo). → Implementar serviço que injeta `ConfiguracaoEmail` no mailer + endpoint de teste real.

3. **Templates de e-mail ausentes (ALTO):** Model `EmailTemplate` órfão. Falta CRUD, editor rich-text, toggle ativo, painel de variáveis, substituição `{var}` e os 4 disparos automáticos (novo colaborador, promoção, fechamento, ocorrência). Mapeamentos de variáveis documentados na seção 4.

4. **Push / OneSignal totalmente ausente (ALTO):** Models órfãos (`OnesignalConfig`, `OnesignalSubscription`, `PushSubscription`). Faltam: tela de config (App ID, REST API Key, Safari Web ID — esta nem existe no model), status/dispositivos, e a tela de envio com filtros, estatísticas e envio individual/lote via API REST do OneSignal. Proteger com `papel:ADMIN,RH`.

5. **Gestão de usuários incompleta (MÉDIO):** Falta **campo de foto** (upload), **campo de setor** dependente da empresa com obrigatoriedade p/ GESTOR, e a **regra "não-ADMIN exige ≥1 empresa"**.

6. **Minha Conta — lacunas (MÉDIO):** (a) provável **bug** no form de senha (`current_password` na view vs `senha_atual` no controller); (b) falta **edição de telefone** do colaborador vinculado; (c) card de perfil não exibe role/empresa/setor/último acesso.

7. **Escopo de acesso por setor/colaborador (MÉDIO):** Faltam equivalentes a `can_access_setor()` e `can_access_colaborador()` (escopo de GESTOR por setor e COLABORADOR só o próprio). Validar se policies de colaborador aplicam isso.
