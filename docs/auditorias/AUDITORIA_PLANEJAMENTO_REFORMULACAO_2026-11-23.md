# 🔎 Auditoria do Planejamento de Reformulação — Programa de Apoio ao Curso Técnico em Enfermagem

**Objeto:** planejamento de reformulação do projeto para o curso Técnico em Enfermagem, a ser
publicado em **<https://enfermagem.appservice.tec.br>** (novo projeto), mantendo
**<https://en430.appservice.tec.br>** estacionado, ativo e inalterado.

| | |
|---|---|
| **Data da auditoria** | 23 de novembro de 2026 |
| **Documentos auditados** | [`PRD_APOIO_FORMACAO_TECNICA_ENFERMAGEM.md`](../planejamento/PRD_APOIO_FORMACAO_TECNICA_ENFERMAGEM.md) (v1.0, 2026-11-21) · [`EXPANSAO_CURSO_TECNICO_ENFERMAGEM.md`](../planejamento/EXPANSAO_CURSO_TECNICO_ENFERMAGEM.md) (v1.0, 2026-11-21) · [`ementa.md`](../ementa.md) · [`RM-01`](../GOVERNANCA_PROGRESSO.md) · [`ESTADO_ATUAL.md`](../ESTADO_ATUAL.md) · `pesquisas/` |
| **Escopo** | Planejamento (currículo, conteúdo, avaliação, produto, arquitetura, infraestrutura, governança). **Não** audita o código do EN_430, já coberto pelas auditorias técnicas de 2026-11-21/22 |
| **Método** | Leitura integral dos documentos; conferência aritmética de todas as somas de carga, itens, materiais e prazos; confronto entre documentos e com as fontes de verdade; verificação de fontes normativas em sítios oficiais em 2026-11-23; teste de disponibilidade da nova URL |
| **Parecer** | 🟪 **Plano consistente na direção, ainda não aprovável.** 49 achados — 17 altos, 25 médios, 7 baixos. Nenhum exige mudar a visão do produto; todos são corrigíveis antes do gate G0 |
| **Achado bloqueante nº 1** | A matriz de 17 componentes não foi validada contra PPC real (Q1 aberta) — todo o dimensionamento depende dela |
| **Achado bloqueante nº 2** | A infraestrutura do novo projeto não está isolada nem publicada: a URL responde **HTTP 503** em 2026-11-23 |

---

## 1. Sumário executivo

### 1.1 O que o planejamento acerta

1. **Limite legal explícito.** O PRD separa com clareza o que o produto é e não é, veda certificação,
   crédito, equivalência e conduta clínica, e prevê disclaimer em produto e exports. Isso é raro e
   correto para material de apoio.
2. **Estágio parametrizável.** A distinção entre 1.200h nacionais (CNCT) e 600h de referência do
   projeto está correta e impede o erro mais comum: gravar 600h no código.
3. **Arquitetura evolutiva.** Evoluir o monólito PHP com PostgreSQL em vez de reescrever é a decisão
   de menor risco e preserva histórico, equipe e deploy.
4. **Governança já instituída.** A RM-01 existe e é aplicável; o planejamento apenas não foi
   enquadrado nela — o que esta auditoria corrige com um artefato de controle próprio.
5. **Aritmética consistente.** Todas as somas do planejamento fecham (seção 3).

### 1.2 O que impede a aprovação hoje

| # | Impedimento | Consequência |
|:-:|---|---|
| 1 | Matriz curricular não validada contra PPC (Q1) | 4.400 itens, 88 materiais e 11 meses dimensionados sobre base não aprovada |
| 2 | Normas ausentes com impacto direto: Lei nº 11.788/2008 (estágio), ABNT NBR 17225:2025 (acessibilidade web), Res. ANPD nº 19/2024 (transferência internacional), Lei nº 14.063/2020 (assinatura eletrônica), Libras | Risco jurídico em estágio, acessibilidade, IA e validade de checklists |
| 3 | Métrica psicométrica inválida para banco multi-tipo (KR-20) | KPI de qualidade mediria something diferente do que promete |
| 4 | Esforço autoral subestimado em ~3× | Cronograma de 11 meses não fecha; conteúdo é o caminho crítico |
| 5 | Duas versões de roadmap com escopos diferentes para o mesmo gate | Duas "verdades" de escopo, proibidas pela RM-01 (§3, P2) |
| 6 | Infraestrutura do novo projeto não isolada nem no ar (503) | Risco de compartilhar banco/segredos com o EN_430 e de confundir os dois produtos |

### 1.3 Recomendação central

Manter a visão e a arquitetura do PRD, e **corrigir o plano antes de iniciar a autoria**:

- congelar a matriz no **gate G0** com PPC/ementa assinados;
- substituir os KPIs psicométricos por métricas válidas para itens politômicos;
- recalcular o esforço autoral e reduzir o MVP ao que a equipe consegue revisar;
- publicar o novo projeto em infraestrutura **isolada** (app, banco, volume, segredos, túnel e
  domínio próprios), com o EN_430 **estacionado** — sem alteração e sem redirecionamento;
- registrar tudo em [`PROGRESSO_PLANEJAMENTO.md`](../planejamento/PROGRESSO_PLANEJAMENTO.md).

---

## 2. Objeto, escopo e limitações

### 2.1 Dois produtos, uma origem

| | EN_430 (existente) | Novo projeto |
|---|---|---|
| URL | <https://en430.appservice.tec.br> | <https://enfermagem.appservice.tec.br> |
| Escopo | Introdução à Enfermagem (120h), 10 módulos, 2.803 registros | Curso Técnico em Enfermagem (1.200h + estágio), 17 componentes |
| Estado desejado | **Estacionado**: no ar, sem evolução, sem exclusão | Em construção, isolado, publicado na URL nova |
| Dados | SQLite `/data` próprio | Banco próprio — **nunca** o mesmo volume |

> **Decisão registrada nesta auditoria:** o EN_430 é **estacionado**, não finalizado nem eliminado.
> Nenhum artefato desta auditoria altera seu código, banco, deploy ou conteúdo. A coexistência é
> permanente até decisão em contrário da direção.

### 2.2 Limitações

- Não houve acesso ao **PPC institucional** nem à coordenação do curso; a matriz auditada é a da
  ementa do projeto, declaradamente uma proposta.
- Não houve entrevistas com estudantes, professores, preceptores ou DPO.
- Pareceres COFEN de 2025/2026 citados pelo PRD foram tratados como **declarados pelo projeto**;
  não foi possível confirmar a íntegra e a vigência de cada um nesta data (achado B8).
- A URL nova foi testada por HTTP e respondeu **503**; não há aplicação publicada para avaliar.
- A auditoria é documental: não executou testes do EN_430 nem alterou ambientes.

---

## 3. Conferência aritmética (evidência)

| Verificação | Cálculo | Resultado |
|---|---|:--:|
| Carga dos módulos | 300 + 330 + 330 + 240 | 1.200h ✅ |
| Itens por módulo | 1.350 + 1.200 + 1.150 + 700 | 4.400 ✅ |
| Materiais por módulo | 26 + 25 + 23 + 14 | 88 ✅ |
| Cobertura declarada | 1 pronto + 5 parciais + 11 inexistentes | 17 ✅ |
| Questões atuais por dificuldade | 1.378 + 739 + 686 | 2.803 ✅ |
| Fichas de pesquisa | 17 matérias + 4 campos | 21 ✅ |
| Blueprint por Bloom | 40% + 40% + 20% | 100% ✅ |
| Duração — PRD | 4 + 12 + 12 + 16 semanas | 44 semanas |
| Duração — plano de expansão | 4 + 10 + 12 + 12 + 8 semanas | **46 semanas** ⚠️ |
| Esforço autoral implícito | 1.800–2.600h ÷ 4.400 itens | **0,41–0,59h/item** ⚠️ |

Duas divergências: o prazo total difere em 2 semanas entre os documentos (A3) e o esforço por item
é incompatível com revisão dupla + metadados + psicometria (C4).

---

## 4. Conformidade com a RM-01

| Princípio / seção | Situação | Evidência |
|---|:--:|---|
| P1 — sem evidência não há progresso | 🟨 parcial | O plano tem metas, mas várias afirmações normativas sem fonte arquivada (B8) |
| P2 — uma fonte de verdade por domínio | ⛔ | Não existe fonte de verdade do **planejamento**; PRD e plano divergem (A1, A3) |
| P3 — atualização no mesmo ato | ⛔ | Estado do sistema evoluiu para PHP 8.6/180 testes e o plano continua em 8.4/158 (A2) |
| P4 — histórico imutável | 🟨 | Planos sem tabela de histórico; versões 1.0 sem registro de alterações (A5) |
| P5 — número com fonte, data e denominador | 🟨 | Metas têm denominador; cobertura e "495 únicos" não têm método (C3) |
| P6 — vocabulário fechado de estado | ⛔ | Planejamento não usa os seis estados da RM-01 em nenhum item (A1) |
| P7 — concluído é binário e comprovado | ✅ | Nada foi declarado concluído — correto |
| §3 — fontes de verdade por domínio | ⛔ | Domínio "planejamento" inexistente na tabela |
| §10 — auditoria mensal ≥ 20% | 🟨 | Primeira auditoria do planejamento é esta; sem histórico anterior |
| §11.11 — prazo só com premissa e responsável | ⛔ | Q1–Q8 e decisões pendentes sem data absoluta (A4) |

**Conclusão:** o planejamento foi produzido **fora** da RM-01. O artefato
`docs/planejamento/PROGRESSO_PLANEJAMENTO.md`, criado nesta auditoria, passa a ser a fonte de
verdade do domínio, com vocabulário, evidência e histórico append-only.

---

## 5. Achados — A. Governança e consistência documental

| ID | Achado | Sev. | Evidência | Recomendação |
|:--:|---|:--:|---|---|
| **A1** | Não existe fonte de verdade do planejamento; a RM-01 §3 cobre produto, pesquisa, changelog e regras, não PRD/expansão | **Alta** | `GOVERNANCA_PROGRESSO.md` §3; ausência de painel de planejamento | Criar `PROGRESSO_PLANEJAMENTO.md` (feito) e propor emenda à RM-01 §3 |
| **A2** | Base de estado desatualizada no plano: cita PHP 8.4 e 158 testes; a realidade é PHP 8.6.0 e 180 testes/469 asserções | **Alta** | `EXPANSAO` linhas 24, 31, 80, 100 × `ESTADO_ATUAL` linhas 280–281 | Atualizar o plano (feito: v1.1) e revisar a cada release |
| **A3** | Dois roadmaps com escopos diferentes para o mesmo gate: plano fase 2 = 6 componentes (inclui Anatomia e SUS); PRD MVP = 4 (sem Anatomia/SUS); prazos 46 × 44 semanas | **Alta** | `EXPANSAO` §7.1 × `PRD` §18.1 | Eleger o PRD como fonte do escopo de release e o plano como fonte da arquitetura; reconciliar em tabela única |
| **A4** | Gates e questões abertas sem data absoluta, sem estado RM-01 e sem aprovação registrada (tabela de aprovação vazia) | **Média** | `PRD` §21.2, §22; `EXPANSAO` §12 | Datas absolutas, dono e estado para Q1–Q8; assinatura de G0 |
| **A5** | Planos sem controle de versão/histórico de alterações (só o PRD tem tabela de controle; nenhum tem histórico append-only) | **Baixa** | `EXPANSAO` cabeçalho e fim | Adotar bloco de controle + histórico nos dois documentos |

---

## 6. Achados — B. Base normativa

O PRD lista 24 normas. A verificação em fontes oficiais em 2026-11-23 confirma o núcleo
(CNCT, DCN EPT, Lei 7.498/86, Decreto 94.406/87, CEPE, Res. 736/2024, LGPD, LBI, ECA,
RDC 36/2013, RDC 222/2018, RDC 15/2012, NR-32) e identifica **oito lacunas**:

| ID | Achado | Sev. | Impacto no produto | Recomendação |
|:--:|---|:--:|---|---|
| **B1** | **Lei nº 11.788/2008 (Lei do Estágio) ausente**, embora o módulo de estágio registre horas, checklists e parecer (FR-27, FR-28, FR-31) | **Alta** | A lei fixa termo de compromisso, jornada de até 6h/dia e 30h/semana, supervisão e seguro. Sem ela, a plataforma pode aceitar registros ilegais e a instituição assume o risco | Citar; validar jornada no registro de horas; modelar termo/seguro como anexos por campo |
| **B2** | **ABNT NBR 17225:2025** (acessibilidade em conteúdo e aplicações web, 146 itens alinhados à WCAG 2.2) e **NBR 17060:2022** (aplicativos móveis) ausentes; o PRD cita só WCAG 2.2 e eMAG | **Alta** | São o parâmetro técnico brasileiro atual do art. 63 da LBI; um PWA mobile-first precisa das duas | Adotar as duas como critério de aceite; usar o checklist da NBR 17225 na auditoria de G1 e G3 |
| **B3** | **Resolução CD/ANPD nº 19/2024** (transferência internacional) ausente, apesar de o PRD prever IA e e-mail transacional possivelmente no exterior | **Alta** | Transferência exige mecanismo válido (decisão de adequação, cláusulas-padrão da ANPD) e cláusulas contratuais em até 12 meses | Incluir no checklist de fornecedores; exigir cláusulas-padrão e região de processamento |
| **B4** | **Lei nº 14.063/2020 (assinatura eletrônica)** ausente, embora FR-27 fale em "assinatura do preceptor" | **Média** | Define níveis e validade da assinatura; sem isso o checklist pode não ter valor probatório institucional | Definir nível exigido (simples/avançada/qualificada) e registrar trilha de autoria |
| **B5** | **Lei nº 10.436/2002 e Decreto nº 5.626/2005 (Libras)** ausentes; Libras aparece como diferencial de R2 | **Média** | Libras é obrigação legal em educação, não item opcional de roadmap | Elevar para requisito; prever janela de Libras e CAA nos vídeos |
| **B6** | **Lacunas de normas de conteúdo por componente:** Decreto nº 7.508/2011 (redes de atenção) em SUS; Portaria GM/MS nº 529/2013 (PNSP) e RDC ANVISA nº 63/2011 em Biossegurança/Qualidade; Lei nº 14.758/2023 (Política Nacional de Prevenção e Controle do Câncer) em Clínica Médica/Saúde da Mulher; Lei nº 13.819/2019 (prevenção da automutilação e do suicídio) e Lei nº 13.431/2017 (escuta protegida) em Saúde Mental/Criança | **Média** | Conteúdo fica incompleto ou desatualizado justamente nos temas sensíveis e de maior risco | Incluir na matriz normativa por componente e no plano de unidade |
| **B7** | Portaria GM/MS nº 3.088/2011 citada sem a **versão consolidada** (Portaria de Consolidação nº 3/2017); a vigilância normativa não inclui a **revisão das DCN da EPT em curso no CNE (2026)** | **Média** | Citação desatualizada em conteúdo publicado; mudança curricular pode invalidar a matriz | Citar a consolidação; adicionar a DCN EPT ao norm watch com dono e cadência |
| **B8** | Pareceres COFEN de 2025/2026 (19/2025, 38/2026), a minuta do novo CEPE e a revisão da RAPS são afirmados sem arquivamento da fonte (PDF + data + hash) | **Baixa** | Link oficial pode mudar; sem cópia datada não há evidência reproduzível (RM-01 P1) | Arquivar fonte primária em `pesquisas/fontes/` com data de acesso e hash |

> **Verificações realizadas nesta data:** Res. CNE/CP nº 1/2021 revoga a Res. CNE/CEB nº 6/2012
> (confirmado no DOU); ABNT NBR 17225:2025 publicada em 11/03/2025 (confirmada em ABNT/Ceweb.br/MDHC);
> Res. CD/ANPD nº 19/2024 publicada em 23/08/2024, com retificação em 18/08/2025 (confirmada no DOU);
> Lei nº 14.758/2023 trata de **prevenção e controle do câncer** — não de saúde mental, o que reforça
> a necessidade de conferir a ementa antes de citar.

---

## 7. Achados — C. Currículo e conteúdo

| ID | Achado | Sev. | Evidência | Recomendação |
|:--:|---|:--:|---|---|
| **C1** | A matriz de 17 componentes e o rateio de horas (ex.: Anatomia 60h, Fundamentos 120h) são propostas sem fonte declarada por linha e sem PPC validado (Q1 aberta) | **Alta** | `EXPANSAO` §6; `PRD` §21.2 | Coluna "fonte do rateio" (PPC/CNCT/consenso) e gate G0 bloqueante com PPC assinado |
| **C2** | Sobreposição curricular não mapeada: Biossegurança × CCIH × CME × NR-32; Farmacologia × Clínica Médica; Anatomia × Fundamentos | **Alta** | `EXPANSAO` §6.1–6.4 | Regra de propriedade: **1 componente primário por item** + N tags transversais; relatório de sobreposição |
| **C3** | Deduplicação dos 2.803 registros não tem método: não define normalização, similaridade, critério de fusão nem destino das variações | **Média** | `EXPANSAO` §13 ("inventariar e deduplicar os 495"); `PROGRESSO` pendente | Método documentado (normalização + similaridade + revisão humana), relatório de reconciliação e número auditável |
| **C4** | Esforço autoral subestimado: 1.800–2.600h para 4.400 itens = 0,41–0,59h/item, incluindo revisão técnica, pedagógica, metadados e psicometria | **Alta** | `EXPANSAO` §6.7, §7.3 | Recalcular para 1,5–3h/item (≈6.600–13.200h) ou reduzir meta/fases; orçar por item revisado |
| **C5** | "88 materiais" e "unidades" usados como sinônimos; não há granularidade definida (unidade de 10–15 min, material, artefato) | **Média** | `PRD` §8.1 ("88 materiais/unidades") | Definir unidade de aprendizagem e contar unidades e materiais separadamente (RM-01 C7) |
| **C6** | Licença CC BY-NC-SA 4.0 proposta antes da decisão de modelo (Q2); NC pode inviabilizar uso institucional/comercial e reuso por redes públicas | **Baixa** | `PRD` §5.7, §21.2 | Decidir modelo e licença **antes** de contratar autoria; licença separada para texto e para itens |
| **C7** | Os 28 materiais legados (12 HTML + 16 PDF) são tratados como "reaproveitáveis" sem auditoria de conteúdo, fontes, licença e acessibilidade | **Média** | `EXPANSAO` §3.1 | Auditoria dos 28 artefatos na Fase 0, com decisão manter/revisar/descartar por arquivo |

---

## 8. Achados — D. Avaliação e psicometria

| ID | Achado | Sev. | Evidência | Recomendação |
|:--:|---|:--:|---|---|
| **D1** | **KR-20 é inválido para o banco multi-tipo proposto.** KR-20 pressupõe itens dicotômicos; com múltipla resposta, cálculo, ordenação e caso, a confiabilidade deve usar ômega de McDonald ou alfa de Cronbach (ou TRI) | **Alta** | `PRD` §7.2, §12.3 ("KR-20 ≥ 0,70") × FR-12 | Substituir o KPI; manter KR-20 apenas para subconjuntos dicotômicos |
| **D2** | Metas psicométricas sem desenho de coleta: p-value, discriminação e confiabilidade exigem N mínimo por item; TRI exige centenas de respostas | **Alta** | `PRD` §12.3; `EXPANSAO` §6.7 | Definir N ≥ 30 por item para teoria clássica (≥ 200 para TRI), agregação por coorte e limiar de decisão |
| **D3** | "Domínio ≥ 70%" sem definição operacional (quantos itens, qual erro, por unidade ou competência, com reteste?) | **Média** | `PRD` §7.1–7.2 | Regra publicada: ≥ 80% em ≥ 10 itens da competência + reteste em 7 dias, com intervalo de confiança |
| **D4** | Bases percentuais inconsistentes: blueprint 40% recordação + 40% aplicação + 20% análise = 60% aplicação/análise; KPI exige "≥ 30% de aplicação/análise" | **Média** | `EXPANSAO` §6.8 × `PRD` §7.2 | Uma única especificação (por Bloom **e** por unidade), com somas verificadas |
| **D5** | Repetição espaçada em **SM-2**, algoritmo de 1987; não há parâmetro por usuário nem critério de eficácia | **Média** | `PRD` §8.1, `EXPANSAO` §5.6 | Adotar **FSRS** (aberto, estado da arte) com fallback SM-2 e medição de retenção |
| **D6** | Sem política de exposição de itens (rotação, limite por item/ano, aposentadoria) | **Baixa** | `PRD` §12.2 | Definir exposição máxima e fila de aposentadoria antes dos simulados recorrentes |

---

## 9. Achados — E. Arquitetura, dados, operação e isolamento

| ID | Achado | Sev. | Evidência | Recomendação |
|:--:|---|:--:|---|---|
| **E1** | Migração SQLite→PostgreSQL com "dual-write/dual-read" é frágil: escrita dupla em dois bancos gera divergência silenciosa e dobra o risco de perda | **Alta** | `EXPANSAO` §11, item 4 | Janela de manutenção com cutover único, reconciliação por somas exatas e rollback ensaiado com data |
| **E2** | Sem TCO: "orçar por pessoa-mês" sem faixa; infra (PostgreSQL, Redis, S3, e-mail, IA) sem custo teto nem gatilho de revisão | **Média** | `PRD` §18.3; NFR-14 | Tabela de custo mensal por cenário (50/500/5.000 estudantes) e gatilho de revisão |
| **E3** | Metas de performance irreais para 3G (p95 ≤ 2 s, LCP ≤ 2,5 s) sem orçamento de bytes por rota | **Média** | NFR-01 | Budgets por rota (JS ≤ 150 KB gzip; LCP ≤ 2,5 s em 4G e ≤ 4 s em 3G) medidos em Lighthouse CI |
| **E4** | NFR-03 (5.000 estudantes / 500 concorrentes) sem teste de carga, sem decisão de sessão (Redis × sessão PHP) e sem SLO por endpoint | **Média** | NFR-03, NFR-09 | Teste de carga no gate R1 e definição de sessão antes do MVP |
| **E5** | Observabilidade sem SLI/error budget, plantão ou resposta a incidentes | **Média** | NFR-09 | 3 SLIs (disponibilidade, latência de avaliação, taxa de erro) + error budget e runbook |
| **E6** | Backup com RPO 24h/RTO 4h sem escopo por entidade (progresso, notas, auditoria, estágio) e sem dono do teste mensal | **Média** | NFR-10, NFR-11 | RPO/RTO por entidade; teste de restauração com data, dono e evidência |
| **E7** | Staging citado como dependência, mas sem requisito de promoção, migração e mascaramento de dados | **Baixa** | `EXPANSAO` §5.8 | FR de promoção + seed sintético + proibição de dados reais em staging |
| **E8** | **Isolamento do novo projeto não especificado e não publicado.** A URL nova responde **HTTP 503** em 2026-11-23; não há decisão escrita sobre app/banco/volume/segredos/túnel/observabilidade separados do EN_430 | **Alta** | Teste HTTP desta auditoria; ausência de documento de infraestrutura | Plano de infraestrutura isolada (criado: `INFRAESTRUTURA_NOVO_PROJETO.md`) com critérios de aceite e checklist |

---

## 10. Achados — F. Segurança, privacidade e IA

| ID | Achado | Sev. | Evidência | Recomendação |
|:--:|---|:--:|---|---|
| **F1** | A dívida de segurança do produto atual não está no roadmap: fallbacks de `ADMIN_SECRET`/`SECRET_KEY`, conta admin única, comparação com `===` e senha mínima de 4 caracteres | **Alta** | Auditoria de credenciais 2026-11-23, A1–A5 | "Fase 0 — hardening" com itens verificáveis e teste que falha se o segredo não vier do ambiente |
| **F2** | Menores de idade sem regra operacional (LGPD art. 14: melhor interesse + esforço razoável de verificação do responsável); o PRD diz "quando exigido" | **Alta** | `PRD` §5.6 | Política etária explícita: faixa, fluxo de consentimento do responsável, revogação e dados de contato |
| **F3** | Trilhas adaptativas e recomendações podem ser decisão automatizada (LGPD art. 20) sem explicabilidade nem revisão humana | **Média** | `PRD` §8.1, FR-15/16 | Exibir critério da recomendação e oferecer revisão/ajuste pelo professor |
| **F4** | IA sem avaliação de fornecedor: falta checklist de DPA, região, retenção zero, subprocessadores e teste de PII | **Média** | FR-40; `PRD` §17.3 | Checklist de fornecedor + teste de vazamento antes de habilitar IA |
| **F5** | Base legal e política de privacidade desatualizadas para o novo escopo (professores, preceptores, turmas, estágio) e para o **reuso de dados do EN_430** em nova finalidade | **Média** | `POLITICA_DE_PRIVACIDADE.md` v1.0 (só estudantes) | Nova política por papel e finalidade; reuso do EN_430 só com base legal e transparência |
| **F6** | NFR-04 exige 2FA para staff e revisão de acessos, mas não há FR correspondente nem requisito de revisão periódica de privilégios | **Média** | NFR-04 × §14 | Criar FR de 2FA e revisão trimestral de acessos, com evidência |

---

## 11. Achados — G. Acessibilidade e inclusão

| ID | Achado | Sev. | Evidência | Recomendação |
|:--:|---|:--:|---|---|
| **G1** | Acessibilidade fica como "baseline" no MVP e a auditoria completa só no R2 (G3); a LBI art. 63 e a NBR 17225 não admitem "depois" | **Alta** | `PRD` §18.1 (R2: "auditoria de acessibilidade") | Critérios por feature desde o MVP, auditoria manual + com usuários no **G1**, não no G3 |
| **G2** | PDFs e materiais legados provavelmente sem tags/ordem de leitura (PDF/UA); não há métrica de legibilidade nem teste com estudantes | **Média** | 16 PDFs em `materiais/pdf/`; `PRD` §11.4 | Meta PDF/UA, regeneração pelo pipeline e índice de legibilidade (Flesch-PT ≥ 60) nas telas e materiais |

---

## 12. Achados — H. Viabilidade, cronograma e equipe

| ID | Achado | Sev. | Evidência | Recomendação |
|:--:|---|:--:|---|---|
| **H1** | Cronograma sem folga e sem gatilho numérico de replanejamento ("parar/replanejar" sem limiar) | **Alta** | `PRD` §18.4; `EXPANSAO` §7.1, §10 | Buffer de 15% por fase e gatilho: componente < 70% dos itens na data → replanejar |
| **H2** | Time autoral (8 professores × 10h/semana) não contratado; não há RACI nem instrumento aplicável a autores (só validador) | **Média** | `PRD` §18.3; `docs/juridico/` | RACI por componente e termo de autor cessão antes da primeira unidade |
| **H3** | MVP com 4 componentes + PWA offline + autoria completa + LGPD + acessibilidade + admin em 12 semanas para 1 backend, 1 frontend e 0,4 QA | **Média** | `PRD` §18.1–18.3 | Cortar do MVP: PWA offline e autoria completa (usar importação validada); devolver no R1 |
| **H4** | Gantt inicia em 2026-12-01 sem G0 aprovado; o cronograma nasce atrasado por definição | **Baixa** | `EXPANSAO` §7.2 | Datas derivadas do G0 assinado, não de calendário fixo |

---

## 13. Achados — I. Produto, adoção e diferenciação

| ID | Achado | Sev. | Evidência | Recomendação |
|:--:|---|:--:|---|---|
| **I1** | Sem plano de adoção/sustentação: treinamento de professores, onboarding, suporte e CSAT são metas sem ação datada | **Média** | `PRD` §7.2, §19 | Plano de lançamento por turma com treinamento, material de apoio e canal de suporte |
| **I2** | Nenhuma pesquisa com estudantes antes do MVP; as dores são assumidas | **Média** | `PRD` §6 | 8–12 entrevistas e teste de usabilidade antes do G1 |
| **I3** | Diferenciação frágil diante de YouTube/PDF/apps genéricos: falta declarar o "melhor em uma coisa" | **Baixa** | `PRD` §4.3 | Eleger um diferencial verificável (ex.: banco alinhado ao PPC + feedback por competência) e medir |

---

## 14. Consolidado

| Dimensão | Altos | Médios | Baixos | Total |
|---|:--:|:--:|:--:|:--:|
| A. Governança e consistência | 3 | 1 | 1 | 5 |
| B. Base normativa | 3 | 4 | 1 | 8 |
| C. Currículo e conteúdo | 3 | 3 | 1 | 7 |
| D. Avaliação e psicometria | 2 | 3 | 1 | 6 |
| E. Arquitetura e isolamento | 2 | 5 | 1 | 8 |
| F. Segurança, privacidade e IA | 2 | 4 | 0 | 6 |
| G. Acessibilidade | 1 | 1 | 0 | 2 |
| H. Viabilidade e equipe | 1 | 2 | 1 | 4 |
| I. Produto e adoção | 0 | 2 | 1 | 3 |
| **Total** | **17** | **25** | **7** | **49** |

**Bloqueiam o gate G0:** A1–A4, B1–B3, C1, C2, C4, E8, F1, F2, G1, H1.
**Corrigíveis durante a Fase 0:** todos os demais.

---

## 15. Matriz normativa revisada (norma → uso → componente)

Normas **já previstas** no PRD (mantidas) e as **incorporadas** por esta auditoria:

| Norma | Uso no produto | Componente / requisito | Situação |
|---|---|---|:--:|
| LDB 9.394/1996; CNCT Res. CNE/CEB 2/2020; DCN EPT Res. CNE/CP 1/2021 | Enquadramento, 1.200h, competências | Matriz; FR-06/07 | ✅ |
| Lei 7.498/1986; Decreto 94.406/1987 | Limites de escopo e supervisão | Todo o conteúdo | ✅ |
| **Lei 11.788/2008** | Termo, jornada 6h/dia, 30h/semana, seguro, supervisão | Estágio; FR-28/31 | ➕ **nova** |
| **Lei 14.063/2020** | Nível de assinatura do checklist/parecer | FR-27/29 | ➕ **nova** |
| Res. COFEN 564/2017 (CEPE) e 736/2024 (PE) | Ética, sigilo, SAE | Ética; Fundamentos | ✅ |
| Parecer Normativo COFEN 001/2019 | Estágio parametrizável | FR-09 | ✅ |
| Lei 8.080/1990; 8.142/1990; **Decreto 7.508/2011** | SUS, redes, contratualização | SUS e Saúde Coletiva | ➕ complemento |
| Lei 10.216/2001; **Lei 13.819/2019**; Portaria 3.088/2011 (consolidada) | Reforma psiquiátrica, RAPS, prevenção ao suicídio | Saúde Mental | ➕ complemento |
| **Lei 14.758/2023** | Prevenção e controle do câncer | Clínica Médica; Saúde da Mulher | ➕ **nova** |
| **Portaria GM/MS 529/2013; RDC ANVISA 63/2011** | PNSP; boas práticas em serviços de saúde | Biossegurança; campos de estágio | ➕ **nova** |
| RDC 36/2013; RDC 222/2018; RDC 15/2012; NR-32 | Segurança do paciente, PGRSS, CME, biossegurança | Biossegurança; Cirúrgica/CME | ✅ |
| ECA 8.069/1990; **Lei 13.431/2017** | Proteção integral, escuta protegida | Criança/Adolescente; Saúde Mental | ➕ complemento |
| LGPD 13.709/2018; **Res. ANPD 19/2024** | Dados pessoais, transferência internacional | FR-03/35/36; fornecedores | ➕ **nova** |
| LBI 13.146/2015; **NBR 17225:2025; NBR 17060:2022**; WCAG 2.2; eMAG | Acessibilidade web e móvel | FR-39; G1/G3 | ➕ **nova** |
| **Lei 10.436/2002; Decreto 5.626/2005** | Libras | Vídeos; R2 → MVP | ➕ **nova** |
| Lei 9.610/1998 | Direitos autorais e licenças | Autoria; FR-13 | ✅ |
| **Lei 12.965/2014** (Marco Civil) | Guarda de logs, responsabilidade | Auditoria; NFR-05 | ➕ complemento |
| **Lei 14.533/2023** (PNED) | Educação digital | Posicionamento do produto | ➕ contexto |
| AHA 2025; PNI; PNH | Diretrizes de conteúdo | Urgência; Criança; Psicologia | ✅ |
| **Proposta de DCN EPT (CNE, 2026)** | Monitoramento | Norm watch | 👁 monitorar |

---

## 16. KPIs corrigidos (substituem os do PRD §7.2)

| Categoria | KPI atual | Problema | KPI proposto |
|---|---|---|---|
| Qualidade | KR-20 ≥ 0,70 | Inválido para itens politômicos | **Ômega de McDonald ≥ 0,70** na prova completa; KR-20 só no subconjunto dicotômico |
| Qualidade | Discriminação ≥ 0,20 | Não distingue dicotômico/politômico | Ponto-bisserial (dicotômico) e correlação item-resto (politômico) ≥ 0,20 |
| Qualidade | 100% dos itens aplicados com análise psicométrica | Impossível no 1º ano com turmas pequenas | **100% dos itens aplicados com N ≥ 30 respostas**; fila de calibração para o resto |
| Aprendizagem | Domínio ≥ 70% | Sem regra operacional | **≥ 80% em ≥ 10 itens da competência**, com reteste em 7 dias |
| Conteúdo | ≥ 30% aplicação/análise | Base diferente do blueprint (60%) | **≥ 60% aplicação/análise no blueprint** e ≥ 30% por unidade |
| Aprendizagem | Repetição espaçada (SM-2) | Algoritmo legado | **FSRS**, com retenção-alvo de 0,90 e medição de carga de revisão |
| Acessibilidade | WCAG 2.2 AA | Não cobre a norma brasileira | **NBR 17225 (regular) + NBR 17060** + auditoria manual e com usuários |
| Performance | p95 ≤ 2 s em 3G | Sem orçamento de bytes | LCP ≤ 2,5 s em 4G / ≤ 4 s em 3G; JS ≤ 150 KB gzip por rota |
| Operação | Uptime ≥ 99,5% | Sem SLI/error budget | 3 SLIs + error budget mensal e runbook de incidente |

---

## 17. Gates revisados (com datas absolutas)

| Gate | Critério de saída | Bloqueia | Data proposta |
|:--:|---|:--:|:--:|
| **G0-A** | PPC/ementa assinados; matriz e rateio com fonte por linha; licença e modelo decididos | Currículo | 2026-12-18 |
| **G0-B** | Disclaimer e termos aprovados por jurídico; política por papel; regra de menores; ROPA inicial | Conteúdo e dados | 2026-12-18 |
| **G0-C** | Infraestrutura isolada no ar em `enfermagem.appservice.tec.br` (health check 200, banco próprio, segredos próprios, backup testado) | Engenharia | 2026-12-11 |
| **G1** | MVP com componentes definidos no PRD, 100% dos itens revisados, acessibilidade auditada (NBR 17225), 1 turma-piloto | Release | 2027-03-19 |
| **G2** | R1 com 7 componentes, 2.000 itens, ômega ≥ 0,70 no piloto, 2 campos de estágio | Release | 2027-06-11 |
| **G3** | R2 com 17/17, banco completo, OSCE/portfólio, auditoria de acessibilidade e plano de sustentação | Release | 2027-10-01 |

---

## 18. Plano de ação priorizado

### 18.1 Próximos 15 dias (até 2026-12-08)

| # | Ação | Achado | Dono | Evidência de conclusão |
|:-:|---|:--:|---|---|
| 1 | Aprovar o orçamento e o plano da infraestrutura isolada; criar app/projeto no Coolify e rota no túnel | E8 | Tech lead | `INFRAESTRUTURA_NOVO_PROJETO.md` executado; `GET /` = 200 na URL nova |
| 2 | Congelar `en430` como estacionado (sem deploy automático, sem alteração de conteúdo) | E8 | Tech lead | Configuração de deploy alterada e registrada |
| 3 | Nomear donos de G0-A/G0-B/G0-C e abrir Q1–Q8 com datas absolutas | A4 | Direção | Tabela de Q com data e dono |
| 4 | Corrigir roadmap único (PRD como fonte de escopo; plano como fonte de arquitetura) | A3 | Direção + tech lead | Tabela única de releases |
| 5 | Recalcular esforço autoral por item e revisar prazo/meta | C4, H1 | Líder pedagógico | Planilha de esforço com premissas |
| 6 | Iniciar hardening da base que será herdada (segredos, admin, senha mínima) | F1 | Tech lead | Teste que falha sem segredo no ambiente |

### 18.2 Até 2027-01-15

| # | Ação | Achado | Dono |
|:-:|---|:--:|---|
| 7 | Validar matriz e rateio contra PPC e assinar G0-A | C1 | Coordenação |
| 8 | Publicar política de privacidade por papel + regra de menores | F2, F5 | DPO/Jurídico |
| 9 | Incorporar as 8 normas novas à matriz e ao norm watch | B1–B8 | Coordenação + enfermeiro RT |
| 10 | Definir método de deduplicação e executá-lo nos 495 enunciados | C3 | Responsável de conteúdo |
| 11 | Auditar os 28 materiais legados (conteúdo, licença, acessibilidade) | C7 | Conteúdo + acessibilidade |
| 12 | Trocar KPIs psicométricos e definir N mínimo por item | D1, D2 | Líder pedagógico |
| 13 | Fechar licença e modelo de oferta | C6 | Direção + jurídico |

### 18.3 Até 2027-03-19 (G1)

| # | Ação | Achado | Dono |
|:-:|---|:--:|---|
| 14 | Auditoria de acessibilidade manual + com usuários no MVP | G1, G2 | Designer + QA |
| 15 | Teste de carga e definição de SLO/error budget | E4, E5 | Tech lead |
| 16 | Pesquisa com estudantes e teste de usabilidade | I2 | Product owner |
| 17 | Plano de adoção por turma e treinamento de professores | I1 | Product owner |
| 18 | Checklist de fornecedor de IA (DPA, região, retenção) | F4 | DPO + tech lead |

---

## 19. Riscos residuais desta auditoria

| Risco | Efeito | Tratamento |
|---|---|---|
| PPC real divergir da ementa do projeto | Retrabalho de matriz, itens e materiais | G0-A bloqueante; nada de autoria antes da assinatura |
| Pareceres COFEN de 2025/2026 não confirmados na íntegra | Conteúdo de ética/estágio desatualizado | Arquivar fonte primária com data e hash (B8) antes de publicar |
| Equipe autoral não ser contratada no prazo | Cronograma inviável | Decisão de reduzir meta de itens ou fases (C4, H1) |
| URL nova permanecer em 503 | Sem ambiente para piloto e para o mock | Plano de infraestrutura com critério de aceite (E8) |

---

## 20. Registro de interações e ações desta auditoria

Registro completo, com estado RM-01 e histórico append-only, em
[`PROGRESSO_PLANEJAMENTO.md`](../planejamento/PROGRESSO_PLANEJAMENTO.md). Resumo:

| # | Ação | Resultado |
|:-:|---|---|
| 1 | Leitura integral de PRD, plano de expansão, ementa, RM-01, ESTADO_ATUAL e pesquisas | Base de evidência levantada |
| 2 | Conferência aritmética de cargas, itens, materiais, cobertura e prazos | 8 somas corretas; 2 divergências (prazo e esforço) |
| 3 | Confronto entre documentos e fontes de verdade | 3 divergências de estado (A2, A3, D4) |
| 4 | Verificação de fontes normativas em sítios oficiais (CNE, ANPD, ABNT, Planalto) | 8 lacunas normativas; 4 citações confirmadas |
| 5 | Teste de disponibilidade da nova URL | `HTTP 503` em 2026-11-23 |
| 6 | Emissão deste parecer e criação do artefato de controle do planejamento | Auditoria registrada; 49 achados |
| 7 | Correção factual do plano de expansão (PHP 8.6; 180 testes/469 asserções) | Versão 1.1 do plano |
| 8 | Elaboração do plano de infraestrutura isolada e do mock da landing page | Documentos criados |

---

## 21. Referências

- **Resolução CNE/CP nº 1/2021** (DCN EPT; revoga a Res. CNE/CEB nº 6/2012) — DOU 06/01/2021: <https://www.in.gov.br/en/web/dou/-/resolucao-cne/cp-n-1-de-5-de-janeiro-de-2021-297767578>
- **Resolução CD/ANPD nº 19/2024** (transferência internacional; retificação de 18/08/2025): <https://www.in.gov.br/web/dou/-/resolucao-cd/anpd-n-19-de-23-de-agosto-de-2024-580095396>
- **ABNT NBR 17225:2025** — acessibilidade em conteúdo e aplicações web (146 itens; base WCAG 2.2): <https://www.gov.br/mdh/pt-br/assuntos/noticias/2025/marco/com-apoio-do-governo-federal-nova-norma-tecnica-da-abnt-e-instituida-para-impulsionar-acessibilidade-digital-no-brasil>
- **ABNT NBR 17060** — acessibilidade em dispositivos móveis (referência no CGI.br): <https://www.cgi.br/media/docs/publicacoes/4/pt-br/20260619113019/Nota%20t%C3%A9cnica%20CGI.br%20sobre%20recomenda%C3%A7%C3%B5es%20de%20normas%20t%C3%A9cnicas%20para%20acessibilidade%20digital.pdf>
- **Lei nº 14.758/2023** — Política Nacional de Prevenção e Controle do Câncer: <https://www.planalto.gov.br/ccivil_03/_ato2023-2026/2023/lei/l14758.htm>
- **Lei nº 11.788/2008** — estágio: <https://www.planalto.gov.br/ccivil_03/_ato2007-2010/2008/lei/l11788.htm>
- **Lei nº 14.063/2020** — assinatura eletrônica: <https://www.planalto.gov.br/ccivil_03/_ato2019-2022/2020/lei/l14063.htm>
- **Decreto nº 7.508/2011** — regulamenta a Lei nº 8.080/1990: <https://www.planalto.gov.br/ccivil_03/_ato2011-2014/2011/decreto/d7508.htm>
- **Lei nº 13.819/2019** — prevenção da automutilação e do suicídio: <https://www.planalto.gov.br/ccivil_03/_ato2019-2022/2019/lei/l13819.htm>
- **Lei nº 10.436/2002** e **Decreto nº 5.626/2005** — Libras: <https://www.planalto.gov.br/ccivil_03/leis/2002/l10436.htm>
- **CNE — proposta de atualização das DCN da EPT (2026):** <https://www.gov.br/mec/pt-br/cne/2026/agosto-2026/pcp013_26.pdf>
- Projeto: [`GOVERNANCA_PROGRESSO.md`](../GOVERNANCA_PROGRESSO.md) · [`ESTADO_ATUAL.md`](../ESTADO_ATUAL.md) · [`RELATORIO_SEGURANCA_STACK_2026-11-22.md`](RELATORIO_SEGURANCA_STACK_2026-11-22.md) · [`AUDITORIA_SENHA_SAMBA_WINDOWS_2026-11-23.md`](AUDITORIA_SENHA_SAMBA_WINDOWS_2026-11-23.md)

---

*Auditoria documental. Não substitui parecer jurídico, validação do PPC nem auditoria de acessibilidade
com usuários. Achados sem evidência apontável não foram incluídos.*

