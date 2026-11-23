# 🖼️ RM-04 — Ilustração Obrigatória e Recursos Visuais da Pesquisa

**Status:** vigente · **Versão:** 1.0 · **Vigência:** imediata a partir de **2026-11-23**
**Aprovação:** decisão da direção do projeto
**Abrangência:** toda pesquisa de matéria, todo artefato didático, todo item de avaliação, todo
material de apoio e todo formulário produzido a partir deles

> **Regra de base e imutável.** A ilustração não é enfeite nem etapa opcional do fim: é parte do
> conteúdo. Cada matéria é elaborada **com** o seu banco de imagens, do mesmo modo que é elaborada
> com fontes. Pesquisa sem banco de imagens é **rascunho**, nunca 🟪 nem ✅.

**Relação com as demais regras:**

| Regra | O que governa | Relação com esta |
|-------|---------------|-----------------|
| [RM-01](GOVERNANCA_PROGRESSO.md) | Medição, registro e auditoria | O banco de imagens entra no registro da matéria e no histórico append-only |
| [RM-02](RM-02_PRECISAO_DIDATICA_E_VERACIDADE.md) | Precisão, didática e veracidade | Imagem é conteúdo: vale B1/B2 e a proibição de figura sem licença (§5.4) |
| [RM-03](RM-03_RESPONSIVIDADE.md) | Responsividade | Vale M6: imagem responsiva, proporção preservada e `alt` descritivo |

Esta RM **complementa** as três anteriores; não reduz nem flexibiliza nenhuma delas.

---

## 1. A regra

| ID | Regra | Consequência |
|:--:|-------|--------------|
| **I1** | Toda matéria tem **banco de imagens versionado** em `<matéria>/imagens/`, com manifesto `README.md` | Matéria sem banco de imagens não avança de 🟦 para 🟪 |
| **I2** | Nenhuma imagem entra sem **licença verificada e registrada** | Sem autor, licença, link da fonte, data e hash, o arquivo não é usado |
| **I3** | Toda imagem tem **função pedagógica declarada** e unidade de destino | Imagem decorativa é descartada; se não fixa nada, não entra |
| **I4** | Toda imagem tem **legenda didática** e **texto alternativo** | Figura sem descrição textual não é publicada (RM-02 D7, RM-03 M6) |
| **I5** | A imagem não pode **ensinar errado nem sugerir o que não é verdade** | Contexto estrangeiro é rotulado; texto em outro idioma é traduzido na legenda; esquema não substitui norma |
| **I6** | O banco de imagens é **copiado para o repositório**, nunca embutido por link externo | `hotlink` e "imagem do Google" são proibidos; o link serve como fonte, não como origem do arquivo |

## 2. Requisitos mínimos (mensuráveis)

| # | Requisito | Medida |
|:-:|-----------|--------|
| V1 | Cobertura mínima por matéria | **≥ 4 imagens** licenciadas e **≥ 1 imagem para cada 2 unidades** |
| V2 | Cobertura das unidades de ação | Toda unidade que envolve **técnica, procedimento ou decisão** tem pelo menos uma imagem |
| V3 | Licença aceita | Domínio público, CC0, CC BY, CC BY-SA, OGL ou órgão oficial com crédito — registrada no manifesto |
| V4 | Resolução | lado maior ≥ **1000 px**; preferência por 1200–1600 px; proporção preservada |
| V5 | Peso | ≤ **1,5 MB** por arquivo; total do banco ≤ **12 MB** por matéria |
| V6 | Nome do arquivo | `snake_case`, sem acento, descrevendo o conteúdo (`higienizacao_das_maos.png`) |
| V7 | Rastreabilidade | URL da página de origem, autor, licença, data da consulta, dimensões e **SHA-256** |
| V8 | Acessibilidade | `alt` descrevendo o conteúdo visível; legenda dizendo **o que observar** |
| V9 | Uso previsto | a imagem é citada em pelo menos um artefato (material, item ou mapa de unidades) |
| V10 | Substituição | troca de arquivo entra como **linha nova** no registro da matéria (RM-01 §9), com o motivo |

## 3. Licenças: o que pode e o que não pode

| Situação | Decisão |
|----------|---------|
| Domínio público / CC0 | **Aprovado**, sem obrigação de crédito — crédito ainda é registrado por honestidade |
| CC BY (qualquer versão) | **Aprovado**, com crédito completo (autor, licença, link) |
| CC BY-SA (qualquer versão) | **Aprovado**, com crédito e aviso: derivado editado deve manter a mesma licença |
| Reprodução de figura de artigo, livro ou diretriz | **Proibido** sem autorização escrita; descrever em texto ou produzir figura própria |
| "Fair use", "para fins educacionais", sem licença declarada | **Proibido** — não é licença |
| Captura de tela de site, sistema ou material de terceiros | **Proibido** como figura didática |
| Banco de imagens com termo restritivo ou marca em destaque | **Proibido** |
| Foto de paciente identificável | **Proibido**, qualquer que seja a licença |
| Figura gerada por IA | **Restrita**: só como esquema, rotulada como gerada, revisada por enfermeiro, nunca como evidência |

**Fontes preferenciais:** Wikimedia Commons (metadados de licença na própria página), acervos
públicos nacionais (Fiocruz, IBGE, Agência Brasília, museus), órgãos oficiais (MS, ANVISA, OPAS)
e **figura própria** desenhada no projeto.

## 4. Fidelidade: o erro que a imagem bonita esconde

1. **Contexto.** Foto produzida em outro país não representa automaticamente a prática brasileira.
   A legenda declara o contexto: "imagem de contexto internacional; não representa a rotina
   brasileira".
2. **Idioma.** Diagrama com rótulos em inglês exige legenda que traduza os termos usados, ou
   substituição por figura própria em português na fase de produção do material.
3. **Norma.** Nenhum esquema, infográfico ou foto substitui o texto da norma vigente: a imagem
   ilustra, a fonte decide (RM-02 §4).
4. **Escopo profissional.** A imagem não pode mostrar o técnico executando ato privativo do
   enfermeiro como se fosse rotina autônoma; quando o ato é supervisionado, a legenda diz isso.
5. **Simplificação.** Esquema didático que corta exceções leva a nota "simplificação didática"
   (RM-02 P6).

## 5. Formato do manifesto (`<matéria>/imagens/README.md`)

Tabela 1 — inventário técnico:

| Arquivo | Autor | Licença | Fonte (página de origem) | Dimensões | SHA-256 (12) |
|---------|-------|---------|--------------------------|-----------|--------------|

Tabela 2 — uso pedagógico:

| Arquivo | Unidade | O que mostra | O que fixa | Legenda sugerida | Texto alternativo |
|---------|:-------:|--------------|------------|------------------|-------------------|

Seções obrigatórias: **restrições e cuidados**, **lacunas** (o que falta e por quê) e **bloco de
créditos** pronto para colar no material final.

## 6. Procedimento (por matéria)

1. Listar as unidades e marcar as que exigem imagem (V2).
2. Buscar candidatas em fonte com licença declarada; conferir autor e licença **na página**.
3. Baixar em 1200–1600 px, nomear em `snake_case`, calcular o hash.
4. Verificar a imagem visualmente: mostra o que se pretende? tem texto em outro idioma? tem pessoa
   identificável? é de contexto estrangeiro?
5. Escrever legenda, `alt` e função pedagógica; ligar a imagem a um artefato (V9).
6. Registrar no manifesto (tabelas 1 e 2) e no registro da matéria.
7. Atualizar `PROGRESSO.md` e o catálogo [`pesquisas/RECURSOS_VISUAIS.md`](../pesquisas/RECURSOS_VISUAIS.md).

## 7. Checklist de conformidade (por matéria)

- [ ] ≥ 4 imagens, com cobertura de todas as unidades de ação (V1–V2).
- [ ] Licença, autor, link, data e hash registrados para cada arquivo (V3, V7).
- [ ] Arquivos no repositório, sem hotlink (I6).
- [ ] Legenda e `alt` escritos para cada imagem (V8).
- [ ] Contexto estrangeiro e idioma estrangeiro declarados (I5).
- [ ] Nenhuma imagem de paciente identificável, marca em destaque ou figura sem licença.
- [ ] Cada imagem citada em pelo menos um artefato (V9).
- [ ] Lacunas registradas com o que falta e quem resolve.
- [ ] Manifesto, registro da matéria e `PROGRESSO.md` atualizados.

## 8. Conformidade e consequências

- Matéria sem banco de imagens fica em 🟦, no máximo; não recebe 🟪.
- Imagem sem licença registrada é **removida** do repositório e o fato entra no histórico.
- Imagem que ensine errado (contexto não declarado, rótulo não traduzido, ato fora de escopo) é
  **corrigida ou retirada** antes da publicação, com registro da correção.

## 9. Controle do documento

| Versão | Data | Autor | Alteração |
|:------:|------|-------|-----------|
| 1.0 | 2026-11-23 | Direção do projeto | Criação da regra de ilustração obrigatória e recursos visuais (I1–I6, V1–V10) |

*Regra de base imutável: complementável, não revogável.*
