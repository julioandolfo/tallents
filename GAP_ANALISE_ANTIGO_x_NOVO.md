# Análise de Gap — Sistema Antigo (rh-privus, produção) × Novo (Tallents Gestão)

> Fonte: dump de produção (185 tabelas) + código do repositório `rh-privus`.
> O **código** do repo antigo era só o núcleo de RH; a **produção** evoluiu para
> uma plataforma completa. O novo sistema migrou fielmente o **núcleo** e os
> **dados**. Abaixo, o que falta para igualar a produção.

---

## ✅ Já migrado (núcleo de RH — 100%)
Empresas · Setores · Cargos · Níveis hierárquicos · Colaboradores · Usuários ·
Ocorrências (básico) · Tipos de ocorrência · Horas extras (básico) · Promoções ·
Tipos de bônus · Bônus de colaboradores · Fechamento de pagamento (básico) ·
Configuração de e-mail · Templates de e-mail · **+ todos os dados importados**.

---

## ⚠️ Existe no novo, mas o antigo é bem mais avançado
| Módulo | No novo | No antigo (produção) também tem |
|---|---|---|
| **Ocorrências** | registro simples | advertências, **flags/sinais**, categorias, anexos, comentários, **histórico**, **regras de advertência automática**, tags, templates de descrição, campos dinâmicos, notificações |
| **Horas extras** | lançamento simples | **Banco de horas** (saldo + movimentações), solicitações (aprovação), saldo em dinheiro |
| **Fechamento de pagamento** | totais básicos | **adiantamentos**, **pagamentos PJ** (solicitações + horas + log), config de extras, histórico de documentos, importação de extrato, **centros de custo** |
| **Push/Notificações** | tabelas existem, sem tela | envio real (OneSignal/Web Push) + histórico + preferências |

---

## ❌ Módulos inteiros que faltam (não existem no novo)

### 1. LMS / Universidade Corporativa (treinamentos)
Cursos, aulas, categorias, certificados, progresso do colaborador, cursos
obrigatórios + regras + alertas, comentários/favoritos, player com auditoria e
segurança (anti-fraude). *(15 tabelas)*

### 2. Gamificação + Loja de recompensas
Pontos (config, histórico, total), **badges/conquistas**, e **Loja** (produtos,
categorias, resgates, wishlist, avaliações, log). *(12 tabelas)*

### 3. Recrutamento & Seleção (ATS)
Vagas (+ etapas, + **geração por IA**, + **landing pages** de vaga / portal),
candidatos, candidaturas (anexos, comentários, etapas, histórico), entrevistas,
processo seletivo. *(15 tabelas)*

### 4. Chat / Atendimento interno (com IA)
Conversas, mensagens, participantes, categorias, respostas rápidas, mensagens
automáticas, **resumos por IA**, SLA + histórico, preferências. *(12 tabelas)*

### 5. Contratos & Assinatura digital
Contratos, signatários, eventos, templates + integração **Autentique**. *(5 tabelas)*

### 6. Feedback · Avaliações · PDI · Cultura
Feedbacks (solicitações, itens, respostas, avaliações), avaliações de desempenho,
**PDI** (objetivos + ações), formulários de cultura, **pesquisas de satisfação** e
**pesquisas rápidas** (envios + respostas). *(18 tabelas)*

### 7. Comunicação interna / Mural / Endomarketing
Comunicados (+ leitura), **Feed/mural social** (posts, curtidas, comentários),
endomarketing (ações + tarefas), eventos (+ participantes), celebrações, datas
comemorativas, **manual de conduta** (+ FAQ + visualizações), manuais individuais,
anotações internas. *(20+ tabelas)*

### 8. Clima / Engajamento / Humor
Humor diário, pesquisas de humor, emoções, config de engajamento.

### 9. Onboarding
Onboarding (+ tarefas + histórico).

### 10. Integrações
**WhatsApp (Evolution API)** · **Slack** · **OpenAI/IA** (prompts + rate limit) ·
Central de **notificações in-app**.

### 11. Outros
**Demissões/Desligamento** · **Dependentes** (filhos) e **Formações** do colaborador ·
**Kanban** (colunas + automações) · Auditoria de acessos · Agendador (cron) ·
Preferências de dashboard.

---

## Dimensão do que falta
- **~15 módulos** completos não existem no novo.
- A produção tem **185 tabelas**; o novo cobre o núcleo (~25).
- Os **dados** desses módulos antigos **não foram importados** (não há onde colocá-los ainda).

## Sugestão de priorização (do mais usado/impactante p/ o menos)
1. **Ocorrências avançadas** (flags, advertências automáticas, anexos) — evolução do que já existe.
2. **Banco de horas** + solicitações (aprovação de horas extras).
3. **Comunicação** (comunicados + mural/feed) — alto uso, baixo esforço.
4. **Onboarding** + **Demissões** (ciclo de vida do colaborador).
5. **Recrutamento (ATS)** — módulo grande, alto valor.
6. **LMS** (treinamentos) — módulo grande.
7. **Feedback/Avaliações/PDI**.
8. **Gamificação + Loja**.
9. **Chat interno**, **Contratos/Assinatura**, **Integrações** (WhatsApp/Slack/IA).
