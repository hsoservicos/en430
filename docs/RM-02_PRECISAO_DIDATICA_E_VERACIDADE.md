# 🎯 RM-02 — Regra de Base de Precisão, Didática e Veracidade

**Status:** vigente · **Versão:** 1.0 · **Vigência:** imediata a partir de **2026-11-23**
**Aprovação:** decisão da direção do projeto
**Abrangência:** todo material didático, pesquisa, item de avaliação, ficha, roteiro, mockup e
texto publicado em nome do projeto

> **Regra de base e imutável.** Estas regras não são revogáveis nem flexibilizáveis: nenhuma
> entrega, prazo, ferramenta ou conveniência as suspende. Elas só podem ser **complementadas** por
> regra nova; nunca reduzidas, reinterpretadas para baixo ou aplicadas "quando der".

**Relação com a RM-01:** a [RM-01](GOVERNANCA_PROGRESSO.md) governa *como o progresso é medido e
registrado*. Esta RM-02 governa *qual conteúdo pode existir*. Em conflito sobre conteúdo, vale a
RM-02; sobre registro e contagem, vale a RM-01.

---

## 1. As duas regras de base

| ID | Regra | Consequência |
|:--:|-------|--------------|
| **B1** | **Ser o mais preciso e o mais didático possível** na elaboração de cada material de matéria/disciplina | Precisão sem didática não ensina; didática sem precisão ensina errado. As duas são obrigatórias ao mesmo tempo |
| **B2** | **Nada é elaborado sem ter sido avaliado e observado quanto à veracidade e à verdade para o aprendizado** | Sem verificação registrada, o material não existe: não é publicado, não entra em item, não vira PDF, slide ou tela |

B2 vale para **todo** conteúdo: unidade de ensino, enunciado, feedback, caso clínico, checklist,
tabela de referência, resumo, material de apoio e texto de interface.

---

## 2. Precisão (como se mede)

| # | Exigência | Evidência aceita |
|:-:|-----------|------------------|
| P1 | Toda afirmação técnica ou normativa é rastreável a **fonte primária** | Órgão, número da norma/edição, data e link, registrados em `fontes/README.md` |
| P2 | Termo técnico é usado com o sentido da fonte, sem sinônimo improvisado | Glossário do material + citação |
| P3 | Número, dose, prazo, faixa de referência e classificação têm **uma** fonte adotada | Tabela "parâmetro → fonte → data" no artefato |
| P4 | Unidade, grandeza e condição do dado são declaradas (ex.: PA em mmHg, em repouso) | Texto do próprio material |
| P5 | Fato, consenso, controvérsia e lacuna são rotulados de forma diferente | Marcadores `[fato]`, `[consenso]`, `[controvérsia]`, `[lacuna]` quando houver dúvida real |
| P6 | Inferência didática (analogia, exemplo, simplificação) não contradiz a fonte | Nota "simplificação didática" quando a simplificação cortar exceções |

## 3. Didática (como se mede)

| # | Exigência | Evidência aceita |
|:-:|-----------|------------------|
| D1 | Objetivo de aprendizagem **observável** por unidade (verbo de ação + condição + critério) | Seção de objetivos do artefato |
| D2 | Progressão do simples ao complexo, com pré-requisito declarado | Mapa de unidades |
| D3 | Um exemplo resolvido e um erro comum por conceito crítico | Material da unidade |
| D4 | Feedback explica **por que** a alternativa está certa/errada, não só o gabarito | Item de avaliação |
| D5 | Linguagem acessível: frase curta, voz ativa, sigla expandida na primeira ocorrência | Revisão de texto |
| D6 | Carga cognitiva controlada: uma ideia principal por tela/seção, no máximo 7 itens por lista | Estrutura do material |
| D7 | Acessibilidade: descrição textual de figura, contraste, leitor de tela, alternativa ao vídeo | Checklist de acessibilidade |
| D8 | Escopo profissional respeitado: o que o técnico **observa, registra e comunica** ≠ o que o enfermeiro **diagnostica e prescreve** | Nota de limite de escopo |

## 4. Verificação de veracidade (o portão obrigatório)

Nenhum material passa para produção sem cumprir, nesta ordem:

1. **Vigência da norma** — conferir no portal oficial se o texto está vigente, com alterações e
   data; registrar a data da consulta.
2. **Consistência científica** — comparar com diretriz nacional e, quando necessário,
   internacional; registrar divergências em vez de escondê-las.
3. **Adequação ao escopo** — o conteúdo cabe no que o Técnico em Enfermagem pode observar,
   registrar e executar sob supervisão? Se não, sai ou é reescrito.
4. **Coerência interna** — o material não contradiz outro material do mesmo curso.
5. **Revisão humana registrada** — enfermeiro com registro ativo (técnica) e educador (pedagógica).
6. **Registro** — fonte, data, revisor e decisão gravados no artefato e no `PROGRESSO.md`.

**Hierarquia de evidência** (prevalece o de cima):

```
norma vigente (federal/estadual/municipal)
  > diretriz nacional (MS, ANVISA, COFEN, MTE)
    > consenso internacional (OMS/OPAS)
      > sociedade científica nacional
        > livro-texto e revisão científica
          > material de terceiros com autoria e data
            > conteúdo gerado por IA (nunca sozinho)
```

Conflito entre fontes: adota-se a de maior hierarquia, a divergência fica registrada e a escolha
justificada. **Não se escolhe a fonte que confirma o texto já escrito.**

---

## 5. Proibições explícitas

1. Publicar conteúdo sem fonte verificada e datada.
2. Citar referência de memória, "de cabeça", sem abrir e conferir o documento.
3. Inventar norma, artigo, DOI, página, edição ou dado estatístico.
4. Reproduzir integralmente obra de terceiros ou figura sem licença.
5. Apresentar simplificação didática como se fosse o texto da norma.
6. Usar conteúdo gerado por IA como fonte de verdade.
7. Prometer certificação, crédito, aprovação ou registro profissional.
8. Prescrever conduta clínica, dose ou diagnóstico (escopo do enfermeiro).
9. Preencher lacuna de conhecimento com suposição plausível.
10. Manter material com erro conhecido no ar "até a próxima revisão".

## 6. Incerteza e correção

- **Lacuna:** registrar `[lacuna]` com o que falta, quem pode responder e o prazo. Nunca inventar.
- **Divergência:** registrar as duas fontes, a data e a decisão.
- **Errata:** erro identificado em material publicado é corrigido **e registrado como linha nova**
  (append-only, RM-01 §9), com data, o que mudou e quais derivados foram afetados (itens, PDFs,
  slides, telas). Correção silenciosa é proibida.
- **Rechecagem:** todo material tem prazo de revisão declarado (padrão: semestral) e um "norm
  watch" quando a norma está em consulta pública ou revisão.

## 7. Checklist de conformidade (por artefato)

- [ ] Cada afirmação normativa tem número, órgão, data e link, conferidos na fonte.
- [ ] Data da consulta registrada em `fontes/README.md`.
- [ ] Normas em revisão/consulta pública anotadas no "norm watch".
- [ ] Objetivos observáveis e pré-requisitos declarados.
- [ ] Exemplo resolvido e erro comum presentes nos conceitos críticos.
- [ ] Feedback explicativo nos itens de avaliação.
- [ ] Limite de escopo profissional declarado.
- [ ] Acessibilidade verificada.
- [ ] Revisão humana (enfermeiro + educador) registrada.
- [ ] Registro atualizado na ficha da matéria e no `PROGRESSO.md`.

## 8. Conformidade e consequências

- Artefato sem fonte verificada é **rascunho**, no máximo 🟦; nunca 🟪 nem ✅.
- Material publicado que viole B1/B2 é **retirado ou sinalizado** até correção registrada.
- A violação entra no histórico do item e na auditoria seguinte (RM-01 §10).

## 9. Controle do documento

| Versão | Data | Autor | Alteração |
|:------:|------|-------|-----------|
| 1.0 | 2026-11-23 | Direção do projeto | Criação da regra de base imutável de precisão, didática e veracidade (B1/B2) |

*Regra de base imutável: complementável, não revogável.*
