# ⚖️ RM-01 — Regra Máxima de Gestão, Controle e Auditoria do Progresso

**Status:** vigente · **Versão:** 1.0 · **Vigência:** imediata a partir de **2026-11-21**
**Aprovação:** decisão da direção do projeto
**Abrangência:** todo o repositório — código, conteúdo, documentação, pesquisa, deploy e operação

> **Esta é a regra definitiva e máxima de acompanhamento de progresso do projeto.** Ela prevalece
> sobre qualquer outra instrução, README, plano, PRD, comentário de código, mensagem de commit ou
> orientação verbal que a contradiga. Em caso de conflito, aplica-se esta regra até que seja
> formalmente emendada (seção 13). Nenhum trabalho é considerado progredido fora do que aqui se
> define.

---

## 1. Princípios fundantes

| ID | Princípio | Consequência prática |
|:--:|-----------|----------------------|
| P1 | **Sem evidência, não há progresso** | Todo avanço declarado aponta para um arquivo, teste, contagem ou registro verificável |
| P2 | **Uma fonte de verdade por domínio** | O mesmo indicador não existe em dois lugares com valores diferentes |
| P3 | **Atualização no mesmo ato** | Quem muda o estado atualiza o registro na mesma sessão/commit — nunca "depois" |
| P4 | **Histórico é imutável** | Linhas de histórico não são apagadas nem reescritas; correções entram como linha nova |
| P5 | **Número sempre com fonte, data e denominador** | "70%" sem base (`x/y`) e sem origem é inválido |
| P6 | **Estado é vocabulário fechado** | Só existem os seis estados da seção 4; não se inventa status |
| P7 | **Concluído é binário e comprovado** | ✅ exige todos os critérios de conclusão atendidos e evidência registrada |

---

## 2. Autoridade e precedência

Para **método e registro**, esta regra prevalece sobre tudo.
Para **o estado do projeto**, a ordem de autoridade é:

1. **Evidência reproduzível** — arquivo versionado, teste executado, comando com saída, contagem
   refeita na hora.
2. **Esta regra** — define como o estado deve ser medido e registrado.
3. **Fonte de verdade do domínio** (seção 3).
4. **Demais documentos** — planos, PRDs, READMEs, auditorias e relatórios.
5. **Mensagens de chat e mensagens de commit** — nunca são fonte de estado.

**Divergência:** se a fonte de verdade afirmar algo que a evidência não sustenta, **a fonte está
errada** até ser corrigida. Corrigir a fonte, registrar no histórico e, se o erro afetou decisão,
abrir item de auditoria.

---

## 3. Fontes de verdade por domínio

| Domínio | Documento de controle | O que registra | Responsável por atualizar |
|---------|----------------------|----------------|---------------------------|
| **Produto, repositório, deploy, números do sistema** | [`docs/ESTADO_ATUAL.md`](ESTADO_ATUAL.md) | URLs, versão publicada, estrutura, contagens verificadas, verificações executadas | Tech lead |
| **Pesquisa e conteúdo curricular** | [`pesquisas/PROGRESSO.md`](../pesquisas/PROGRESSO.md) | Status por matéria/campo, artefatos, pendências, histórico | Responsável de conteúdo |
| **Matéria individual** | `pesquisas/<módulo>/<matéria>/README.md` | Escopo, entregáveis, critérios e registro de execução | Autor da pesquisa |
| **Histórico de versões do produto** | [`CHANGELOG.md`](../CHANGELOG.md) | Mudanças notáveis por versão, no padrão Keep a Changelog | Tech lead |
| **Regras de trabalho e padrões** | [`docs/README.md`](README.md) | Índice, padrões do repositório e convenções | Direção |

**Proibido:** criar um segundo painel, planilha, wiki ou documento paralelo com o mesmo
indicador. Se um documento novo for necessário, ele **substitui** o anterior e o anterior passa a
apontar para o novo no mesmo commit.

---

## 4. Vocabulário único de estado (vale para todo trabalho)

| Símbolo | Estado | Critério de entrada | Critério de saída |
|:-------:|--------|---------------------|-------------------|
| ⬜ | **Não iniciado** | Registro criado, sem execução | Início da execução |
| 🟨 | **Aguardando aprovação** | Escopo/plano proposto e registrado | Aprovação explícita |
| 🟦 | **Em execução** | Trabalho em andamento com registro | Artefatos produzidos |
| 🟪 | **Em revisão** | Artefatos prontos, revisão/validação pendente | Revisão concluída e registrada |
| ✅ | **Concluído** | **Todos** os critérios de conclusão atendidos + evidência + registro | Reabertura por regressão (volta para 🟦/🟪 com registro) |
| ⛔ | **Bloqueado** | Dependência externa identificada, com dono e prazo | Dependência resolvida |

Regras do vocabulário:

- Um item tem **um único** estado por vez.
- 🟪 não é ✅: artefato produzido por IA, autor ou terceiro **não é** entrega validada.
- ⛔ exige nomear a dependência, o dono e a data de revisão.
- Regressão reabre o item; nunca se mantém ✅ com defeito conhecido.

---

## 5. Registro obrigatório (o "ato de progresso")

Todo avanço é registrado com **quatro campos**, sem exceção:

| Campo | Conteúdo mínimo |
|-------|-----------------|
| **Estado** | Um dos seis estados da seção 4 |
| **Evidência** | Caminho de arquivo, link, comando executado ou contagem reproduzível |
| **Data** | Data do ato (AAAA-MM-DD) |
| **Próximo passo** | Ação concreta seguinte ou "nenhuma" |

Formato padrão de linha de histórico:

```
| AAAA-MM-DD | <item> | <etapa> | <resultado com números e evidência> |
```

**Proibido** registrar avanço apenas em mensagem de chat ou apenas na mensagem de commit.

---

## 6. Definição universal de "feito"

| Tipo de trabalho | Só é ✅ quando |
|------------------|----------------|
| **Código** | Implementado + verificado (teste/checagem executada) + documentação atualizada se o comportamento mudou |
| **Conteúdo/pesquisa** | Artefatos produzidos + **revisão humana exigida** concluída + registro e painel atualizados |
| **Documento** | Escrito + vinculado a partir do índice + zero link relativo quebrado |
| **Correção de defeito** | Causa corrigida + verificação refeita + histórico registrado |
| **Deploy** | Publicado + health check/verificação funcional + `ESTADO_ATUAL.md` atualizado |

Sem esses critérios, o estado máximo é 🟪.

---

## 7. Regras de contagem e medição

| Regra | Definição |
|:-----:|-----------|
| C1 | **Item único** = enunciado distinto após deduplicação. Variação textual não conta como item novo. |
| C2 | **Teste** = método de teste versionado; asserções são contadas separadamente. |
| C3 | **Artefato** = arquivo versionado no diretório de artefatos **mais** o registro de fontes. |
| C4 | **Percentual** sempre com base explícita: `x / y (z%)`, uma casa decimal, sem arredondar para cima. |
| C5 | **Toda métrica** declara a fonte (caminho/comando) e a data da medição. |
| C6 | **Nunca** contar arquivo gerado, cache, `vendor/`, duplicata ou material não versionado. |
| C7 | **Nunca** somar duas categorias sobrepostas (ex.: tipo de resposta + estímulo). |

---

## 8. Cadência e gatilhos de atualização

| Gatilho | Ação obrigatória | Prazo |
|---------|------------------|-------|
| Fim de qualquer sessão de trabalho | Atualizar a fonte de verdade do domínio + linha de histórico | Mesma sessão |
| Commit que altera estado do projeto | Atualizar o registro **no mesmo commit** | Imediato |
| Mudança de versão publicada | `CHANGELOG.md` + `ESTADO_ATUAL.md` + versão | Mesmo commit |
| Matéria concluída ou reaberta | `PROGRESSO.md` + `README.md` da matéria | Mesmo commit |
| Revisão periódica do painel | Conferir contadores e somas | Semanal |
| Auditoria de consistência | Amostragem ledger × artefatos (seção 10) | Mensal |
| Auditoria completa | Todos os indicadores e fontes | Trimestral |

---

## 9. Histórico imutável e rastreabilidade

1. Tabelas de histórico são **append-only**: nunca apagar, nunca reescrever, nunca reordenar.
2. Correção de registro anterior entra como **linha nova** com o prefixo `correção:` e referência
   à linha corrigida.
3. Datas não são alteradas retroativamente.
4. Cada item rastreável declara a cadeia completa:
   `fonte oficial → currículo/módulo → componente/matéria → unidade/entrega → evidência`.
5. Nome de arquivo e caminho são parte da evidência: renomear exige atualizar os links e registrar.

---

## 10. Auditoria

**Procedimento mínimo (mensal):**

1. Escolher amostra de **≥ 20%** dos itens em estado ✅.
2. Para cada item, verificar: evidência existe? critérios de conclusão foram atendidos? data
   confere? link resolve?
3. Recontar os indicadores do painel a partir da fonte primária (arquivo, banco, teste).
4. Verificar se o mesmo indicador aparece com valores diferentes em documentos distintos.
5. Registrar resultado, inclusive "sem achados".

**Divergência:** corrigir a fonte de verdade em até **5 dias úteis**, registrar no histórico e,
se o erro influenciou decisão, abrir item na auditoria seguinte.

**Achado que não pode ser corrigido:** o item passa a ⛔ com dependência, dono e data nomeados.

---

## 11. Proibições explícitas

1. Declarar ✅ sem critério de conclusão atendido e sem evidência.
2. Atualizar estado só no chat ou só na mensagem de commit.
3. Publicar métrica sem fonte, sem data ou sem denominador.
4. Apagar ou reescrever linha de histórico.
5. Contar duplicata, variação textual, arquivo gerado ou material não versionado como progresso.
6. Arredondar percentual para cima ou omitir a base de cálculo.
7. Manter dois números diferentes para o mesmo indicador em documentos distintos.
8. Manter ✅ com pendência bloqueante aberta — nesse caso o estado é 🟪 ou ⛔.
9. Criar estado, símbolo ou categoria fora da seção 4.
10. Publicar conteúdo sem a revisão humana exigida pelo PRD.
11. Prometer prazo ou resultado sem registrar a premissa e o responsável.
12. Mover meta ou critério para fazer um item "passar" — mudança de meta é decisão registrada,
    não ajuste silencioso.

---

## 12. Papéis

| Papel | Responsabilidade sobre o progresso |
|-------|------------------------------------|
| **Direção** | Aprova esta regra e suas emendas; decide mudança de meta; recebe auditoria |
| **Tech lead** | Mantém `ESTADO_ATUAL.md`, versão, deploy e métricas técnicas |
| **Responsável de conteúdo** | Mantém `PROGRESSO.md` e a consistência das fichas de pesquisa |
| **Autor/pesquisador** | Atualiza o registro da matéria e fornece evidência do que produziu |
| **Revisor/auditor** | Executa a amostragem, registra achados e verifica correções |
| **Qualquer colaborador** | Não declara progresso fora desta regra; sinaliza divergência quando encontrar |

---

## 13. Emenda desta regra

- Alteração só por **decisão explícita da direção**, registrada.
- Toda emenda: incrementa a versão, atualiza a data de vigência e entra no `CHANGELOG.md`.
- Emenda **nunca** é retroativa: não reescreve histórico anterior.
- Emenda que reduza exigência de evidência precisa de justificativa escrita e fica registrada como
  exceção temporária, com prazo.

---

## 14. Checklist de conformidade (usar em todo commit/entrega)

- [ ] O estado registrado corresponde ao vocabulário da seção 4.
- [ ] Existe evidência apontável (arquivo, comando, contagem).
- [ ] A fonte de verdade do domínio foi atualizada **no mesmo commit**.
- [ ] A linha de histórico foi adicionada com data e resultado numérico.
- [ ] As somas e percentuais foram conferidos e têm base explícita.
- [ ] Nenhum número órfão ou duplicado em outro documento.
- [ ] Nenhum link relativo quebrado.
- [ ] Pendências bloqueantes estão registradas com dono e prazo.

> Um commit que altera estado sem cumprir este checklist é considerado **não conforme** e deve ser
> corrigido antes da próxima entrega.

---

## 15. Aplicação imediata

Na data de vigência, esta regra já se aplica a:

- `docs/ESTADO_ATUAL.md` — estado do produto e números do sistema;
- `pesquisas/PROGRESSO.md` — 17 matérias + 4 campos de estágio;
- fichas de matéria em `pesquisas/**/README.md`;
- `CHANGELOG.md` — histórico de versões.

**Desvios conhecidos e tolerados até a próxima auditoria:** nenhum registrado nesta data.

---

## 16. Controle do documento

| Versão | Data | Autor | Alteração |
|:------:|------|-------|-----------|
| 1.0 | 2026-11-21 | Direção do projeto | Criação da regra máxima de progresso (RM-01) |
