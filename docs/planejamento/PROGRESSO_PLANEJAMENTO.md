# 📊 Controle de Progresso — Planejamento da Reformulação

**Fonte de verdade do domínio "planejamento".** Criado em: **2026-11-23**
**Regra aplicável:** [**RM-01 — Governança de Progresso**](../GOVERNANCA_PROGRESSO.md) (v1.1)
**Documentos controlados:** [PRD](PRD_APOIO_FORMACAO_TECNICA_ENFERMAGEM.md) ·
[Plano de expansão](EXPANSAO_CURSO_TECNICO_ENFERMAGEM.md) ·
[Infraestrutura do novo projeto](INFRAESTRUTURA_NOVO_PROJETO.md) ·
[Formulário de colaboradores](PLANEJAMENTO_FORMULARIO_COLABORADORES.md)
**Auditoria de origem:** [AUDITORIA_PLANEJAMENTO_REFORMULACAO_2026-11-23](../auditorias/AUDITORIA_PLANEJAMENTO_REFORMULACAO_2026-11-23.md)

> Este painel nasceu do achado **A1** da auditoria do planejamento: o domínio não tinha fonte de
> verdade e o PRD e o plano de expansão divergiam em estado e escopo. A partir desta data, todo
> avanço do planejamento é registrado aqui, com evidência, estado do vocabulário da RM-01 §4 e
> histórico **append-only**.

---

## 1. Painel

| Indicador | Valor |
|-----------|------:|
| Documentos de planejamento controlados | 4 |
| Achados da auditoria vigente | **49** (17 altos, 25 médios, 7 baixos) |
| Achados bloqueantes do gate G0 | **15** |
| Gates previstos | 6 |
| Gates aprovados (✅) | **0 / 6** |
| Gates em execução (🟦) | **1** (G0-C, parcial) |
| Questões abertas (Q1–Q8) | 8 — **0 decididas** |
| Ações dos próximos 15 dias | 6 — **2 concluídas**, **1 em execução**, 3 não iniciadas |
| Itens do checklist G0-C atendidos | **6 / 9** |

**Próxima ação da fila:** concluir a ação 1 do plano de 15 dias — infraestrutura isolada com
health check 200, banco próprio, segredos próprios e backup testado (G0-C).
**Bloqueio externo:** Q1 (PPC/instituição de referência) impede a aprovação do G0-A e o início da
autoria em escala.

---

## 2. Estado dos gates

Vocabulário da RM-01 §4: ⬜ não iniciado · 🟨 aguardando aprovação · 🟦 em execução ·
🟪 em revisão · ✅ concluído · ⛔ bloqueado.

| Gate | Critério de saída | Data proposta | Estado | Evidência / bloqueio |
|:----:|-------------------|:-------------:|:------:|----------------------|
| **G0-A** | PPC/ementa assinados; matriz e rateio com fonte por linha; licença e modelo decididos | 2026-12-18 | 🟨 | Aguarda Q1–Q3 e Q6; matriz sem fonte por linha (C1) |
| **G0-B** | Disclaimer e termos aprovados; política por papel; regra de menores; ROPA inicial | 2026-12-18 | 🟨 | Política v1.0 cobre só estudantes (F5); regra de menores pendente (F2) |
| **G0-C** | Infraestrutura isolada no ar, health check 200, banco próprio, segredos próprios, backup testado | 2026-12-11 | 🟦 | Mock publicado em 2026-11-23 (`GET /` = 200); banco, segredos e backup são do G1 — ver §5 |
| **G1** | MVP com componentes do PRD, 100% dos itens revisados, acessibilidade NBR 17225, 1 turma-piloto | 2027-03-19 | ⬜ | Depende de G0-A/B/C |
| **G2** | R1 com 7 componentes, 2.000 itens, ômega ≥ 0,70 no piloto, 2 campos de estágio | 2027-06-11 | ⬜ | Depende de G1 |
| **G3** | R2 com 17/17, banco completo, OSCE/portfólio, auditoria de acessibilidade e sustentação | 2027-10-01 | ⬜ | Depende de G2 |

**Regra:** nenhum gate muda para ✅ sem a evidência da coluna própria registrada no histórico
deste documento. Aprovação sem assinatura registrada permanece 🟨.

---

## 3. Questões abertas (Q1–Q8)

| ID | Questão | Dono | Prazo do PRD | Data absoluta | Estado |
|:--:|---------|------|--------------|:-------------:|:------:|
| Q1 | Qual PPC/instituição será a referência oficial? | Direção | Fase 0 | **a definir (G0-A)** | 🟨 |
| Q2 | O produto será gratuito, institucional ou misto? | Direção | Fase 0 | **a definir (G0-A)** | 🟨 |
| Q3 | Qual licença para o conteúdo próprio? | Jurídico | Fase 0 | **a definir (G0-A)** | 🟨 |
| Q4 | Haverá SSO/LTI no R1? | Tech lead | Fase 1 | a definir (G1) | 🟨 |
| Q5 | Qual provedor de IA e com que DPA? | Tech lead/DPO | R2 | a definir (G3) | 🟨 |
| Q6 | Como será a validação de conteúdo por enfermeiros? | Coordenação | Fase 0 | **a definir (G0-A)** | 🟨 |
| Q7 | Quais campos de estágio no piloto? | Articulação | R1 | a definir (G2) | 🟨 |
| Q8 | Haverá relatório de horas com validade institucional? | Coordenação | R1 | a definir (G2) | 🟨 |

**Achado A4 em tratamento:** o PRD usa prazos relativos ("Fase 0", "R1"). As datas absolutas
acima são compromissos propostos por este controle e passam a valer quando a direção registrar a
decisão no histórico. Enquanto isso, o estado permanece 🟨 e a ausência de data é uma pendência
declarada, não um prazo cumprido.

---

## 4. Plano de ação — próximos 15 dias (até 2026-12-08)

Origem: auditoria do planejamento, §18.1.

| # | Ação | Achado | Dono | Estado | Evidência exigida |
|:-:|------|:------:|------|:------:|-------------------|
| 1 | Infraestrutura isolada: projeto/app no Coolify, rota no túnel e URL nova no ar | E8 | Tech lead | 🟦 | `GET https://enfermagem.appservice.tec.br/` = 200 ✅ (mock); falta banco/segredos/backup |
| 2 | Congelar `en430` como estacionado (sem deploy automático, sem alteração de conteúdo) | E8 | Tech lead | ✅ | `coolify app get 2iweyqxekxmminhiwgyv5bvk`: `is_auto_deploy_enabled=false`, etiqueta `estacionado`, `running:healthy`; `GET /` = 200 em 2026-11-23 |
| 3 | Nomear donos de G0-A/B/C e abrir Q1–Q8 com datas absolutas | A4 | Direção | ⬜ | Tabela de Q com data e dono assinada |
| 4 | Roadmap único: PRD como fonte de escopo; plano como fonte de arquitetura | A3 | Direção + tech lead | ✅ | Tabela única de releases (§6) referenciada pelos dois documentos |
| 5 | Recalcular esforço autoral por item e revisar prazo/meta | C4, H1 | Líder pedagógico | ⬜ | Planilha de esforço com premissas (1,5–3 h/item) |
| 6 | Hardening da base herdada (segredos, admin, senha mínima) | F1 | Tech lead | ⬜ | Teste que falha sem segredo no ambiente |

**Não contabilizado como concluído:** a publicação do mock resolve o critério de disponibilidade
do G0-C, mas não substitui os demais itens do gate. A ação 1 segue 🟦.

---

## 5. Checklist do G0-C (infraestrutura)

Fonte: [INFRAESTRUTURA_NOVO_PROJETO.md](INFRAESTRUTURA_NOVO_PROJETO.md) §7.

- [x] `https://enfermagem.appservice.tec.br/` responde **200** com TLS válido e HSTS.
- [x] `http://` redireciona para `https://` (regra em `CF-Visitor` no nginx).
- [ ] `enfermagem-db` **não** expõe porta à internet — sem banco até o G1.
- [x] Nenhum serviço novo monta `/data` ou o volume do EN_430.
- [ ] Segredos gerados aleatoriamente, sem fallback no código — fase estática, sem segredos.
- [ ] Backup diário executado e uma restauração completa validada — G1.
- [x] `en430.appservice.tec.br` continua **200** e inalterado; auto-deploy desativado em
      2026-11-23 (`is_auto_deploy_enabled=false`).
- [x] Landing com disclaimer e `noindex`; link da política publicado em `/politica-de-privacidade/`.
- [ ] Alertas de disponibilidade, erro e backup ativos e testados — G1.

**Contagem:** 6 de 9 itens atendidos. Os três restantes dependem do G1 por decisão de escopo:
banco próprio, segredos e backup/alertas.

---

## 6. Roadmap único (achado A3)

O PRD é a **fonte do escopo de release**; o plano de expansão é a **fonte da arquitetura**. Esta
tabela substitui as duas versões divergentes (MVP de 4 × 6 componentes; 44 × 46 semanas) e é a
única referência de prazo e escopo.

| Release | Janela | Duração | Escopo | Gate |
|:-------:|--------|:-------:|--------|:----:|
| **Fundação** | 2026-11-23 → 2026-12-18 | 4 semanas | Governança, PPC, jurídico, taxonomia, baseline, ADR e hardening | G0-A/B (2026-12-18) · G0-C (2026-12-11) |
| **MVP** | 2026-12-18 → 2027-03-12 | 12 semanas | Fundamentos, Farmacologia, Biossegurança e Ética; 1.100 itens; formativa; autoria; LGPD e acessibilidade básica | G1 (2027-03-19) |
| **R1** | 2027-03-19 → 2027-06-11 | 12 semanas | + Anatomia, SUS e Clínica Médica; 2.000 itens; somativas; estágio piloto; analytics; SSO opcional | G2 (2027-06-11) |
| **R2** | 2027-06-11 → 2027-10-01 | 16 semanas | + 10 componentes; 4.400 itens; OSCE/portfólio; adaptativo; IA beta; Libras | G3 (2027-10-01) |
| **Operação** | a partir de 2027-10-01 | contínuo | Norm watch, psicometria, revisões, suporte e expansão | Revisão semestral |

**Total:** 4 + 12 + 12 + 16 = **44 semanas (~11 meses)**, com o conteúdo como caminho crítico.
O MVP tem **4 componentes**; Clínica Médica entra no R1, totalizando 7.

**Regra de mudança:** alteração de escopo ou prazo entra primeiro nesta tabela e, no mesmo ato,
nos dois documentos. Nenhum dos dois mantém tabela própria de releases.

Confirmação do achado A3: `PRD §18.1` e `EXPANSAO §7.1` passam a apontar para esta seção.

---

## 7. Consistência documental (achados A2, A3 e D4)

| Achado | Situação em 2026-11-23 | Evidência |
|:------:|------------------------|-----------|
| **A2** — plano cita PHP 8.4 e 158 testes | **Corrigido**: plano de expansão v1.1 passa a citar PHP 8.6, 180 testes e 469 asserções | `EXPANSAO_CURSO_TECNICO_ENFERMAGEM.md` linhas do diagnóstico e do controle |
| **A3** — dois roadmaps com escopos diferentes | **Corrigido**: roadmap único na §6 (44 semanas, MVP de 4 componentes); PRD e plano apontam para ele | §6 e §7, ação 4 |
| **D4** — bases percentuais inconsistentes | **Aberto**: blueprint 60% aplicação/análise × KPI de 30% | §16 da auditoria |
| **D1** — KR-20 inválido para itens politômicos | **Aberto**: substituir por ômega de McDonald | §16 da auditoria |

**Fonte dos números do sistema:** `docs/ESTADO_ATUAL.md` (180 testes, 469 asserções, PHP 8.6.0).
O planejamento cita, não recalcula, esses valores.

---

## 8. Bloqueios do G0 (15 achados)

A1, A2, A3, A4 · B1, B2, B3 · C1, C2, C4 · E8 · F1, F2 · G1 · H1.

Cada um permanece aberto até a evidência de fechamento registrada no histórico. A correção de A2
não fecha o achado sozinha: A2 incluía a atualização do plano **e** a revisão a cada release, que
passa a ser verificada neste painel.

---

## 9. Como atualizar

1. Alterar o estado na tabela afetada, sempre com evidência apontável.
2. Atualizar o painel da seção 1 (contadores e percentuais com base explícita).
3. Registrar a mudança no histórico da seção 10 como **linha nova**; nunca editar linha anterior.
4. Atualizar o documento de origem (PRD, plano ou infraestrutura) no mesmo ato.
5. Se a decisão fechar uma questão Q, registrar dono, data e efeito.

**Contagem de itens do checklist:** itens marcados `[x]` sobre o total de itens do G0-C (9).

---

## 10. Riscos de planejamento em acompanhamento

| Risco | Sev. | Estado | Tratamento |
|-------|:----:|:------:|------------|
| PPC real divergir da ementa do projeto | Alta | Aberto | G0-A bloqueante; nenhuma autoria em escala antes da assinatura |
| Pareceres COFEN de 2025/2026 sem fonte arquivada | Baixa | Aberto | Arquivar PDF com data e hash (B8) |
| Equipe autoral não contratada no prazo | Alta | Aberto | Recalcular esforço (C4) e reduzir meta/fases (H1) |
| Cronograma nascer atrasado por iniciar antes do G0 | Baixa | Aberto | Datas derivadas do G0 assinado (H4) |
| Infraestrutura compartilhar banco/segredo com o EN_430 | Crítica | Mitigado | Isolamento I1–I7; verificação no checklist G0-C |

---

## 11. Histórico de atualizações (append-only)

| Data | Item | Etapa | Resultado |
|------|------|-------|-----------|
| 2026-11-23 | — | Criação | Painel criado a partir do achado A1; 49 achados (17/25/7), 15 bloqueantes do G0, 6 gates e 8 questões abertas registrados |
| 2026-11-23 | A2 | Correção | Plano de expansão atualizado para v1.1: PHP 8.6, 180 testes e 469 asserções; controle do documento adicionado |
| 2026-11-23 | G0-C | Evidência | Mock publicado em `https://enfermagem.appservice.tec.br/` com `GET /` = 200, TLS e HSTS; 4 de 9 itens do checklist atendidos |
| 2026-11-23 | — | Governança | RM-01 v1.1 inclui o domínio "planejamento" na tabela de fontes de verdade (§3) e na aplicação (§15) |
| 2026-11-23 | A3 | Reconciliação | Roadmap único publicado na §6: 44 semanas, MVP de 4 componentes, gates G1–G3; PRD e plano de expansão passam a referenciá-lo |
| 2026-11-23 | G0-C | Evidência | Política de Privacidade publicada em `/politica-de-privacidade/`; link do rodapé corrigido; checklist G0-C em 5 de 9 |
| 2026-11-23 | E8 | Congelamento | Auto-deploy do EN_430 desativado no app `2iweyqxekxmminhiwgyv5bvk` (`is_auto_deploy_enabled=false`) e etiqueta `estacionado` aplicada; app `running:healthy` e `GET /` = 200; ação 2 concluída e checklist G0-C em 6 de 9 |
| 2026-11-23 | — | Stack | Verificação da stack: PHP 8.6 + Apache + Composer/PHPUnit mantidos; `pdo_pgsql` e `redis` ainda não instalados na imagem |
