# 🔬 Pesquisas por Matéria — Curso Técnico em Enfermagem

**Criado em:** 2026-11-21
**Base curricular:** [`docs/ementa.md`](../docs/ementa.md) · matriz de referência em
[`docs/planejamento/EXPANSAO_CURSO_TECNICO_ENFERMAGEM.md`](../docs/planejamento/EXPANSAO_CURSO_TECNICO_ENFERMAGEM.md)
**Objetivo:** produzir, matéria por matéria, a pesquisa abrangente e aprofundada que fundamenta a
reformulação do projeto — conteúdo, competências, itens de avaliação, materiais e referências.

Este diretório é a **frente de conteúdo** do projeto. O software continua em
`sistema_avaliacao/`; aqui ficam os insumos que o alimentam.

---

## 1. Documentos de controle

| Documento | Papel |
|-----------|-------|
| [**RM-01 — Governança de Progresso**](../docs/GOVERNANCA_PROGRESSO.md) | **Regra máxima do projeto:** como todo progresso é medido, registrado e auditado. Prevalece sobre este README e sobre o protocolo abaixo |
| [LISTA_MESTRA.md](LISTA_MESTRA.md) | Lista completa de módulos, matérias, assuntos e itens-alvo |
| [PROGRESSO.md](PROGRESSO.md) | **Fonte de verdade do avanço** — status de cada matéria |
| `modulo-*/README.md` | Escopo e ordem de execução de cada módulo |
| `modulo-*/*/README.md` | Ficha de pesquisa da matéria (assuntos, perguntas, fontes, registro) |

Toda alteração de andamento é registrada em `PROGRESSO.md` **e** no registro da própria matéria.

---

## 2. Estrutura de pastas

```text
pesquisas/
├── README.md
├── LISTA_MESTRA.md
├── PROGRESSO.md
├── modulo-1-fundamentos-basicos-e-saude-coletiva/
│   ├── README.md              # escopo e ordem de execução do módulo
│   └── 01-anatomia-e-fisiologia-humana/
│       ├── README.md          # ficha e registro da pesquisa
│       ├── fontes/            # PDFs, links, normas, artigos e notas de leitura
│       │   └── README.md      # registro de fontes (autor, data, link, licença)
│       └── artefatos/         # sínteses, mapas, matriz de competências, propostas
├── modulo-2-bases-tecnicas-e-assistencia-clinica-geral/
├── modulo-3-assistencia-cirurgica-e-ciclos-de-vida/
├── modulo-4-alta-complexidade-urgencia-e-saude-mental/
└── estagio-curricular-supervisionado/
    ├── README.md
    └── <campo-de-estagio>/    # mesma estrutura (README + fontes/ + artefatos/)
```

**Regras de organização**

- Uma pasta por matéria, numerada conforme a lista mestra (`01`–`17`).
- Nada de arquivos soltos na raiz de `pesquisas/`: material novo entra em `fontes/` ou `artefatos/`.
- Nomes de arquivo em `snake_case` e sem acentos: `ficha_materia.md`, `mapa_unidades.md`.
- Cada fonte registrada deve ter autor/órgão, data, link e licença quando aplicável.
- Conteúdo de terceiros não é copiado integralmente; usa-se síntese com citação.

---

## 3. Protocolo de execução (uma matéria por vez)

1. **Seleção:** seguir a ordem de `PROGRESSO.md` (prioridade Alta e progressão dos módulos).
2. **Proposta:** apresentar o escopo da matéria — assuntos da ementa, perguntas de pesquisa,
   fontes prioritárias e entregáveis previstos.
3. **Confirmação obrigatória:** aguardar aprovação explícita antes de iniciar a pesquisa.
4. **Pesquisa:** levantar fontes primárias (legislação, MEC/CNE, MS, ANVISA, COFEN/COREN, MTE,
   OMS/OPAS, sociedades científicas), registrar tudo em `fontes/`.
5. **Síntese:** produzir os artefatos em `artefatos/` — ficha da matéria, mapa de unidades,
   matriz de competências, proposta de itens e de materiais.
6. **Registro:** preencher o registro da matéria e atualizar `PROGRESSO.md` (status, data,
   artefatos, pendências).
7. **Encerramento:** marcar critérios de conclusão e indicar a próxima matéria.

> Sem confirmação no passo 3, nenhuma pesquisa é executada. A confirmação é **por matéria**,
> não por lote.

---

> **Subordinação:** o protocolo acima é operacional. Quanto a registro, evidência, contagem e
> histórico, aplica-se a [RM-01](../docs/GOVERNANCA_PROGRESSO.md) sem exceção.

## 4. Critérios de conclusão de uma matéria

- [ ] Todos os assuntos da ementa mapeados e ampliados.
- [ ] Fontes primárias identificadas, datadas e arquivadas/linkadas.
- [ ] Unidades de ensino propostas com objetivos observáveis.
- [ ] Competências e pré-requisitos declarados.
- [ ] Itens-alvo especificados (quantidade, tipos, Bloom, dificuldade).
- [ ] Materiais e recursos indicados (leitura, caso, flashcards, mídia).
- [ ] Riscos de desatualização e pontos de revisão semestral anotados.
- [ ] Registro da matéria e `PROGRESSO.md` atualizados.

---

## 5. Situação atual

- Matérias mapeadas: **17** + **4 campos de estágio**.
- Fichas de pesquisa criadas: **21** (todas com escopo, unidades, perguntas, fontes prioritárias
  e entregáveis definidos).
- Pesquisas concluídas: **0**.
- Pesquisa em revisão: **[01 — Anatomia e Fisiologia Humana](modulo-1-fundamentos-basicos-e-saude-coletiva/01-anatomia-e-fisiologia-humana/)**
  (7 entregáveis; pré-revisão assistida concluída em `artefatos/revisao_tecnica_pedagogica.md`;
  aguarda validação por enfermeiro e por educador).
- Próxima sugerida: **[03 — Biossegurança e Controle de Infecção (CCIH)](modulo-1-fundamentos-basicos-e-saude-coletiva/03-biosseguranca-e-controle-de-infeccao-ccih/)**.

O acompanhamento detalhado fica em [PROGRESSO.md](PROGRESSO.md).

---

## 6. Como colaborar (professores e gestão educacional)

Este diretório é publicado no repositório para que a validação pedagógica aconteça **antes**
de qualquer produção em escala.

| Papel | O que revisar | Onde começar |
|-------|---------------|--------------|
| **Gestão educacional / coordenação** | Escopo, carga horária, itens-alvo, aderência ao PPC e prioridades | [LISTA_MESTRA.md](LISTA_MESTRA.md) e [PROGRESSO.md](PROGRESSO.md) |
| **Professor-autor** | Unidades, objetivos e materiais da matéria | `modulo-*/README.md` e `artefatos/` da matéria |
| **Enfermeiro revisor** | Correção técnica, limites de escopo profissional e fontes | Ficha da matéria + `artefatos/revisao_tecnica_pedagogica.md` (seção 8 = roteiro de pendências) |
| **Pedagogo / designer instrucional** | Alinhamento construtivo, Bloom, avaliação e acessibilidade | `artefatos/matriz_competencias.md` e `artefatos/proposta_itens.md` |

**Como devolver a avaliação:**

1. Abra uma *issue* ou *pull request* citando **caminho do arquivo + seção** (ex.:
   `pesquisas/.../01-.../artefatos/mapa_unidades.md`, U11).
2. Registre o aceite ou a correção na tabela de histórico da matéria, como **linha nova**
   (formato append-only da [RM-01](../docs/GOVERNANCA_PROGRESSO.md)).
3. Não altere o estado para ✅ sem a validação registrada: artefato produzido **não** é entrega
   validada.

**O que está pronto para teste hoje:** 21 fichas de escopo e os 7 entregáveis da matéria 01
(🟪 em revisão). As demais matérias aguardam confirmação, uma por vez.
