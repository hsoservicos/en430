# Pacote de Validação — Matéria 01 (Anatomia e Fisiologia Humana)

| Campo | Valor |
|---|---|
| **Versão do pacote** | 1.0 |
| **Preparado em** | 2026-11-21 |
| **Estado do envio** | ⛔ **Não enviado** — aguardando designação do(a) professor(a) validador(a) |
| **Destinatário** | [a designar] |
| **Remetente** | Humberto Santos de Oliveira — mantenedor do projeto |
| **Prazo para o parecer** | **2026-12-06** (15 dias corridos, cláusula 9 do termo de adesão); alerta de risco grave em 24h |
| **Commit de referência** | `018887e` |
| **Status da matéria** | 🟪 Em revisão — não concluída até o parecer registrado |

> Este pacote é o **envelope de envio**: reúne os artefatos, a ordem de leitura, as perguntas que se
> espera ver respondidas e o critério de conclusão. Ele **não** substitui o parecer: sem registro,
> a matéria permanece 🟪.

---

## 1. O que vai no envio

| # | Artefato | Linhas | Papel na validação |
|:-:|----------|:------:|--------------------|
| 1 | [`../README.md`](../README.md) | 103 | Escopo, unidades propostas, entregáveis, critérios e pendências |
| 2 | [`../artefatos/ficha_materia.md`](../artefatos/ficha_materia.md) | 128 | Competência geral, 8 objetivos, carga horária e 300 itens-alvo |
| 3 | [`../artefatos/mapa_unidades.md`](../artefatos/mapa_unidades.md) | 195 | 12 unidades com assuntos e aplicação ao cuidado |
| 4 | [`../artefatos/matriz_competencias.md`](../artefatos/matriz_competencias.md) | 82 | Competências por unidade, pré-requisitos e distribuição de Bloom |
| 5 | [`../artefatos/proposta_itens.md`](../artefatos/proposta_itens.md) | 159 | 300 itens especificados por unidade, tipo e dificuldade |
| 6 | [`../artefatos/proposta_materiais.md`](../artefatos/proposta_materiais.md) | 113 | 6 materiais com tempo, licença e versão |
| 7 | [`../fontes/README.md`](../fontes/README.md) | 72 | 18 fontes com autor, data, link e licença |
| 8 | [`../artefatos/revisao_tecnica_pedagogica.md`](../artefatos/revisao_tecnica_pedagogica.md) | 194 | Pré-revisão assistida: 16 achados, correções aplicadas e roteiro de pendências |

**Total: 8 arquivos, 1.046 linhas.**

### 1.1. O que **não** está no pacote

- O **banco de 300 itens** propriamente dito: existe apenas a **especificação** (distribuição por
  unidade, Bloom, dificuldade e tipo de resposta). Nenhum item foi escrito para validação.
- Os **materiais didáticos** M1–M6: estão especificados, não produzidos.
- Links pendentes de confirmação (F3 e F17) e edições dos livros F8–F12.

Se o parecer for favorável, a etapa seguinte é produzir os itens — e eles exigem **nova rodada** de
validação antes de uso por estudantes.

---

## 2. Ordem de leitura sugerida

| Etapa | Ler | Tempo estimado | Pergunta que orienta |
|:-----:|-----|:--------------:|----------------------|
| 1 | `README.md` da matéria | 15 min | O escopo proposto corresponde ao que se espera de um técnico em Enfermagem? |
| 2 | `ficha_materia.md` | 25 min | A competência geral e os 8 objetivos estão observáveis e no nível certo? |
| 3 | `mapa_unidades.md` | 40 min | As 12 unidades cobrem a ementa sem excesso de profundidade nem lacuna? |
| 4 | `matriz_competencias.md` | 20 min | Competências, pré-requisitos e evidências estão coerentes com o mapa? |
| 5 | `proposta_itens.md` | 30 min | A distribuição de itens e de níveis cognitivos mede o que os objetivos prometem? |
| 6 | `proposta_materiais.md` | 20 min | Os 6 materiais são suficientes e executáveis em 60h? |
| 7 | `fontes/README.md` | 20 min | As fontes são oficiais, vigentes e adequadas; as licenças permitem o uso previsto? |
| 8 | `revisao_tecnica_pedagogica.md` | 30 min | A pré-revisão deixou passar algo? As pendências são as corretas? |

**Total estimado: 3h20.** A leitura na ordem proposta evita julgar itens antes de conhecer o escopo.

---

## 3. Perguntas que se espera ver respondidas

### 3.1. Técnicas

1. A **profundidade** de anatomia e fisiologia é a adequada ao técnico em Enfermagem, ou ainda
   invade competência de nível superior?
2. Falta algum sistema ou estrutura exigida pela ementa e pelo CNCT?
3. A **correlação sistema → parâmetro de cuidado** (pulso, PA, ausculta, diurese, LPP) está correta?
4. Existe erro conceitual, imprecisão ou simplificação que induza **prática insegura**?
5. Alguma unidade descreve procedimento **privativo do enfermeiro** como se fosse do técnico?
6. As **faixas de referência** de sinais vitais precisam de fonte única fixada? Qual adotar?
7. A **escala de consciência** deve ser Glasgow ou outra? Qual a da instituição de referência?
8. As fontes F1–F18 estão vigentes; o link de F3 e F17 pode ser confirmado?

### 3.2. Pedagógicas

9. A distribuição de **Bloom** (88 compreender/104 aplicar/82 analisar/26 avaliar) é adequada a 60h?
10. Os 300 itens-alvo são **proporcionais** às 12 unidades e à carga horária?
11. A **sobre-representação** necessária para a avaliação somativa (40% aplicar+analisar) está clara?
12. Os materiais M1–M6 são executáveis no tempo previsto e acessíveis (WCAG 2.2 AA)?

---

## 4. Como emitir e registrar o parecer

1. Assinar, na mesma ocasião, o **TCS** e o **Termo de Adesão do Professor Validador**:
   [`docs/juridico/`](../../../../docs/juridico/README.md).
2. Preencher o **Anexo II** do termo de adesão: achados classificados em alto/médio/baixo,
   checklist de 9 itens e decisão (aprovado / aprovado com ressalvas / reprovado).
3. Registrar o parecer como **linha nova** na tabela de histórico do `README.md` da matéria,
   com data, versão revisada e resultado (formato append-only da
   [RM-01](../../../../docs/GOVERNANCA_PROGRESSO.md)).
4. Enviar o parecer para **juridico@en430.appservice.tec.br** e, havendo achado de risco grave,
   também para **seguranca@en430.appservice.tec.br** em até 24h.
5. A correção da segunda rodada é revisada em até **5 dias úteis** após o envio da versão corrigida.

---

## 5. Critério de conclusão da matéria

A matéria só vai para ✅ quando **todos** os itens abaixo estiverem cumpridos e registrados:

- [ ] Parecer técnico emitido por enfermeiro(a) com **registro ativo**, assinado e registrado.
- [ ] Parecer pedagógico emitido por educador(a), assinado e registrado.
- [ ] Todos os achados **altos e médios** resolvidos ou formalmente aceitos com justificativa.
- [ ] Uma **fonte fixada por parâmetro** de sinais vitais.
- [ ] Escala de consciência definida.
- [ ] Links de F3 e F17 confirmados.
- [ ] Decisão sobre uso comercial (licença CC BY-NC-SA da fonte F7).
- [ ] Acessibilidade de M1 e M2 testada com leitor de tela.
- [ ] Linha de histórico registrada em `README.md` e em
      [`pesquisas/PROGRESSO.md`](../../../PROGRESSO.md).

Enquanto qualquer item estiver aberto, a matéria permanece 🟪 — **artefato produzido não é entrega
validada**.

---

## 6. Checklist de preparação do envio

- [x] Artefatos versionados e publicados (commit `018887e`).
- [x] Links relativos verificados (`scripts/verificar_links.py`: 0 quebrados).
- [x] Pacote descrito, com ordem de leitura e prazo.
- [ ] Designar o(a) professor(a) validador(a) (enfermeiro com registro ativo + educador).
- [ ] Emitir os dois termos jurídicos e colher assinaturas.
- [ ] Enviar o e-mail de convite com o link do diretório `validacao/` e o prazo.
- [ ] Confirmar recebimento e registrar a data de início do prazo.

---

## 7. Rastreabilidade

| Versão | Data | Mudança |
|:------:|------|---------|
| 1.0 | 2026-11-21 | Pacote criado; envio pendente de designação do validador |

Qualquer alteração nos artefatos após o envio gera **nova versão do pacote** e **nova rodada** de
parecer, registrada como linha nova de histórico.
