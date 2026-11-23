# 🔬 Auditoria dos Artefatos de Pesquisa — 17 matérias + 4 campos de estágio

**Data:** 2026-11-23 · **Escopo:** `pesquisas/**` (fichas, `fontes/`, `artefatos/`)
**Regras aplicadas:** [RM-01](../GOVERNANCA_PROGRESSO.md) e
[RM-02](../RM-02_PRECISAO_DIDATICA_E_VERACIDADE.md)
**Natureza:** auditoria de consistência (RM-01 §10), não declaração de progresso

---

## 1. Método

1. Inventário de arquivos por matéria: `find pesquisas -type f`.
2. Verificação estrutural das 21 fichas: presença das 7 seções obrigatórias
   (Escopo na ementa, Unidades propostas, Perguntas de pesquisa, Fontes prioritárias,
   Entregáveis, Critérios de conclusão, Registro de execução).
3. Contagem de entregáveis e de artefatos por matéria (C3 da RM-01: arquivos em `artefatos/`
   + `fontes/README.md`).
4. Conferência do status declarado na ficha contra `pesquisas/PROGRESSO.md` e contra o README
   do módulo.
5. Conferência das somas de CH e itens da `LISTA_MESTRA.md`.

**Comandos de evidência:**

```bash
find pesquisas -type f | sort
grep -c "^- \[ \]" pesquisas/<módulo>/<matéria>/README.md
ls pesquisas/<módulo>/<matéria>/artefatos
grep -n "Status" pesquisas/<módulo>/<matéria>/README.md
```

---

## 2. Resultado do inventário

| Grupo | Fichas | Com artefatos | Sem artefatos | `fontes/` preenchido |
|-------|:------:|:-------------:|:-------------:|:--------------------:|
| Módulo I | 6 | 1 (01) | 5 | 1 (01) |
| Módulo II | 4 | 0 | 4 | 0 |
| Módulo III | 4 | 0 | 4 | 0 |
| Módulo IV | 3 | 0 | 3 | 0 |
| Estágio | 4 | 0 | 4 | 0 |
| **Total** | **21** | **1** | **20** | **1** |

- Artefatos existentes: **7** em 01 (6 em `artefatos/` + `fontes/README.md`), conforme RM-01 C3.
- Inventário levantado no **início** da auditoria; a matéria 03 foi iniciada depois (seção 5) e
  passou a 6 artefatos (5 em `artefatos/` + `fontes/README.md`), totalizando **13** no diretório.
- Artefatos ausentes: **0 de 7** nas outras 20 fichas.
- Estrutura de pastas: **21/21** completas (`README.md`, `fontes/`, `artefatos/`).

---

## 3. Achados

| # | Achado | Gravidade | Situação |
|:-:|--------|:---------:|----------|
| A1 | 20 de 21 fichas não têm **nenhum** artefato nem registro de fontes: a ficha está pronta, a pesquisa não começou | **Alta** (esperado: status ⬜) | Registrado; é o objeto da fila de pesquisa |
| A2 | `fontes/README.md` — entregável obrigatório — existe só na matéria 01 | **Alta** | Passa a ser exigido em toda pesquisa iniciada (RM-02 §4) |
| A3 | README do Módulo I mostrava **01 como ⬜**, divergindo de `PROGRESSO.md` e da ficha (🟪) | **Média** (P2 da RM-01) | **Corrigido** em 2026-11-23 |
| A4 | Fichas de estágio usam template próprio (sem "Escopo na ementa"/"Unidades propostas") | Baixa | **Sem ação**: o template de campo usa "Atividades previstas" e "Competências observáveis", equivalente e adequado |
| A5 | Ficha 07 declara itens como "400 (após deduplicação de 2.803 registros…)", fora do padrão numérico simples | Baixa | **Sem ação**: o valor-alvo coincide com a `LISTA_MESTRA` (400) |
| A6 | Nenhuma ficha registrava **data de consulta e status de vigência** das fontes | **Alta** (RM-02 B2) | Corrigido no padrão a partir da matéria 03 |
| A7 | Matéria 01 mantém 2 pendências de link (COFEN 001/2019 e CNE/CP 2/2024) e segue 🟪 | Média | Mantido 🟪 — pendência de revisão humana, não de estrutura |

**Somas verificadas (LISTA_MESTRA):** CH 300+330+330+240 = **1.200h** ✓ · itens
1.350+1.200+1.150+700 = **4.400** ✓ · 17 matérias + 4 campos ✓.

**Consistência de status:** 20 fichas ⬜ (correto) e 1 🟪 (01, com artefatos e pendência de
validação humana). Após esta auditoria, a matéria 03 passou a 🟪 com artefatos produzidos.

---

## 4. Conformidade com a RM-02 (a partir de 2026-11-23)

| Exigência | Antes | Depois |
|-----------|-------|--------|
| Fonte primária aberta, datada e com vigência conferida | só em 01, sem data de consulta | padrão obrigatório em toda pesquisa; 03 já cumpre |
| Nada elaborado sem verificação | não formalizado | portão da RM-02 §4 no protocolo de pesquisa |
| Revisão humana antes de publicar | prevista | mantida: artefato produzido = 🟪, nunca ✅ |

---

## 5. Encaminhamento

1. A fila de pesquisa segue a ordem de `PROGRESSO.md` (prioridade Alta, progressão de módulo).
2. Matéria 03 iniciada nesta data com fontes verificadas e 6 entregáveis — ver
   [`pesquisas/modulo-1-fundamentos-basicos-e-saude-coletiva/03-biosseguranca-e-controle-de-infeccao-ccih/`](../../pesquisas/modulo-1-fundamentos-basicos-e-saude-coletiva/03-biosseguranca-e-controle-de-infeccao-ccih/).
3. Próximas da fila: 04, 05, 07, 08, 10, 11, 15 (Alta), depois 02, 06, 09, 12, 13, 14, 16, 17.
4. A 01 continua aguardando validação humana (prazo do parecer: 2026-12-06).

---

## 6. Controle

| Versão | Data | Autor | Alteração |
|:------:|------|-------|-----------|
| 1.0 | 2026-11-23 | Pesquisa assistida (revisão humana pendente) | Auditoria inicial dos 21 artefatos; 7 achados; 1 correção aplicada (A3) e 1 correção de padrão para as próximas (A6) |
