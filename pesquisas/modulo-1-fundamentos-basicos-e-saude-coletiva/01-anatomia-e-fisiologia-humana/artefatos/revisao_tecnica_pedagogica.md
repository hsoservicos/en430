# Revisão Técnica e Pedagógica — 01 Anatomia e Fisiologia Humana

**Data:** 2026-11-21 · **Escopo:** os 7 entregáveis da matéria (ficha, 5 artefatos e registro de fontes)
**Natureza:** **pré-revisão assistida**. Não substitui a revisão técnica obrigatória por
enfermeiro com registro ativo nem a revisão pedagógica por educador (PRD, seção 11.2).
**Veredito:** aprovado com correções — **16 achados**, todos tratados; 0 bloqueantes;
4 pendências dependem de validação humana.

---

## 1. Método

1. Leitura integral de cada artefato contra a ementa (`docs/ementa.md`) e o PRD.
2. Conferência aritmética de todas as distribuições (horas, itens, Bloom, dificuldade, tipos).
3. Checagem normativa das afirmações legais em fonte oficial (COFEN, MEC/CNE, Planalto).
4. Checagem de coerência interna entre artefatos (mesmo número em documentos diferentes).
5. Checagem pedagógica: alinhamento construtivo, carga cognitiva, qualidade de item, feedback,
   acessibilidade e conteúdo sensível.

---

## 2. Sumário dos achados

| Severidade | Achados | Corrigidos | Pendentes de humano |
|:----------:|:-------:|:----------:|:-------------------:|
| Alto | 5 | 4 | 1 |
| Médio | 7 | 7 | 0 |
| Baixo | 4 | 4 | 0 |
| **Total** | **16** | **15** | **1** |

---

## 3. Achados de severidade alta

### A1 — Coleta de Papanicolau atribuída ao técnico · **corrigido**

- **Artefato:** `mapa_unidades.md` (U11) e `ficha_materia.md` (limites de escopo).
- **Problema:** a unidade listava "coleta de Papanicolau" como aplicação da matéria, o que pode
  ser lido como atribuição do técnico.
- **Base:** Resolução COFEN nº 381/2011, art. 1º — "no âmbito da equipe de Enfermagem, a coleta
  de material para colpocitologia oncológica pelo método de Papanicolaou é **privativa do
  Enfermeiro**". Reforçado pelo Parecer Técnico COREN/PR nº 15/2022.
- **Correção:** passou a "**auxílio** no exame (posicionamento, material, orientação)" com aviso
  explícito de que a coleta é privativa; incluído nos limites de escopo da ficha.

### A2 — Distribuição de Bloom incoerente entre dois artefatos · **corrigido**

- **Artefatos:** `matriz_competencias.md` (progressão por bloco) × `proposta_itens.md` (tabela global).
- **Problema:** a progressão por bloco (92 / 154 / 54 itens) implicava **88/104/82/26** por nível;
  a proposta global dizia **90/90/75/45**. O nível *analisar* divergia em 19 itens (26 × 45),
  e o total de aplicação/análise caía de 40% para 36% sem que nenhum documento registrasse isso.
- **Correção:** `proposta_itens.md` passou a 88/104/82/26 (29%/35%/27%/9%) e a matriz ganhou a
  tabela de cálculo auditável. Aplicar + analisar = **108 itens (36%)**, ainda acima da meta de
  30% do PRD.

### A3 — Falta de alerta sobre uso da OpenStax em IA · **corrigido**

- **Artefato:** `fontes/README.md`.
- **Problema:** o projeto usa assistência de IA; a OpenStax veda o uso do livro para treinar
  modelos de linguagem sem autorização prévia.
- **Correção:** incluído alerta explícito de que trechos de F7 **não** devem ser colados em
  ferramentas de IA do projeto.

### A4 — Atualização do CNCT de 2024 não registrada · **registrado, pendente**

- **Artefato:** `fontes/README.md` (F17).
- **Problema:** o CNCT foi atualizado pela **Resolução CNE/CP nº 2, de 04/04/2024** (incorporação
  de Áreas Tecnológicas aos Eixos). Os documentos de planejamento citam apenas a 4ª edição de
  2020.
- **Situação:** a mudança é de organização de eixos e não altera o perfil do Técnico em
  Enfermagem nem a carga de 1.200h, segundo o portal do CNCT. Registrado como F17 com pendência
  de link oficial e de confirmação pela coordenação.
- **Ação fora desta matéria:** sinalizar em `docs/planejamento/` que a base normativa tem uma
  atualização de 2024.

### A5 — Meta de 40% da somativa era inatingível pelo banco · **corrigido**

- **Artefatos:** `ficha_materia.md` (6.1) e `matriz_competencias.md` (5).
- **Problema:** exigia-se somativa com ≥ 40% de aplicação/análise **distribuída proporcionalmente**
  a um banco que tem 36%. Proporcionalidade e meta se contradiziam.
- **Correção:** explicitado que o blueprint **sobre-representa** as unidades 5–12 para atingir
  40%, e registrada a decisão.

---

## 4. Achados de severidade média

### M1 — "Turgência" sem qualificação · **corrigido**
U7 listava "turgência" entre parâmetros observados pelo técnico. Turgência jugular é avaliação do
enfermeiro. Passou a constar apenas como conceito, com a técnica fora do escopo.

### M2 — Glasgow sem delimitar registro e interpretação · **corrigido**
U5 passou a explicitar que o técnico **aplica e registra** a escala; a interpretação diagnóstica
é do enfermeiro (COFEN 736/2024, art. 6º).

### M3 — Taxonomia de itens com dupla contagem · **corrigido**
`proposta_itens.md` somava "múltipla escolha", "caso clínico" e "figura" como tipos mutuamente
exclusivos, embora caso e figura sejam **estímulos** com resposta de múltipla escolha. Separado
em dois recortes: tipo de resposta (265 MCQ + 20 associação + 15 ordenação) e estímulo
(240 texto + 15 caso + 10 figura).

### M4 — Item de exemplo violava a própria regra de homogeneidade · **corrigido**
No exemplo 4, a alternativa correta era a mais longa e detalhada — pista clássica de
*test-wiseness*. Alternativas reescritas com extensão semelhante.

### M5 — Ausência de plano de avaliação · **corrigido**
Os artefatos especificavam itens, mas não como eram aplicados. Incluída a seção 6.1 da ficha:
diagnóstica (25), formativa (60), prática (8 estações), somativa (40) e recuperação (15), com a
relação banco × instrumentos.

### M6 — Licença de texto legal sem base citada · **corrigido**
"Domínio público (texto legal)" passou a citar o fundamento: Lei 9.610/1998, art. 8º, IV.

### M7 — Materiais sem tempo, licença e versão · **corrigido**
`proposta_materiais.md` ganhou a seção 3 com tempo estimado de estudo, licença prevista e versão
inicial; o total (~12h de material estruturado para 60h) alimenta o plano semanal do PRD (FR-15).

---

## 5. Achados de severidade baixa

| ID | Achado | Correção |
|:--:|--------|----------|
| B1 | Erro de concordância no feedback do exemplo 4 ("o rim retenção de água") | Reescrito para "o rim retém água" |
| B2 | Critério local de 80% em aplicação apresentado como se fosse meta do PRD | Marcado como **proposta** a validar |
| B3 | U2 incluía "cicatrização" sem delimitar fronteira com a matéria 07 | Delimitado: técnica de avaliação de ferida fica em 07 |
| B4 | Exemplos de item sem metadados de unidade e fonte | Acrescentados `Unidade` e `Fonte` nos 4 exemplos |

---

## 6. Checklist pedagógico

| Critério | Resultado | Observação |
|----------|:---------:|------------|
| Competência geral declarada e observável | ✅ | Verbo de ação, escopo delimitado |
| Objetivos por unidade com verbos observáveis | ✅ | 8 objetivos gerais + objetivo por unidade |
| Alinhamento construtivo (objetivo → item → evidência) | ✅ | Corrigido em A2; matriz rastreia unidade → evidência |
| Progressão cognitiva adequada | ✅ | Análise só a partir de U5 |
| Carga cognitiva compatível com 60h | ✅ | Máx. 6 conceitos-chave por sistema |
| Feedback explicativo (não só "errado") | ✅ | 4 exemplos com raciocínio e fonte |
| Variedade de instrumentos | ✅ | Diagnóstica, formativa, prática, somativa, recuperação |
| Critérios de domínio explícitos | ✅ | ≥ 70% (PRD); 80% em aplicação é proposta |
| Acessibilidade (alt, tabelas, tempo adicional) | ⚠️ | Definido, mas **não testado** com leitor de tela |
| Conteúdo sensível tratado com cuidado | ✅ | U11 com privacidade/consentimento; linguagem não discriminatória |
| Nenhuma conduta prescritiva | ✅ | Item 3 e feedback reforçam "comunicar ao enfermeiro" |
| Correspondência integral com a ementa | ✅ | 11 sistemas + organização celular/tecidual + integração |

---

## 7. Checklist técnico-normativo

| Verificação | Resultado |
|-------------|:---------:|
| Soma de horas das unidades = 60h | ✅ 5+4+5+4+6+5+8+6+5+6+4+2 |
| Soma de itens por unidade = 300 | ✅ 24+20+26+22+30+26+40+32+26+28+18+8 |
| Soma por Bloom = 300 | ✅ 88+104+82+26 |
| Soma por dificuldade = 300 | ✅ 120+135+45 |
| Soma por tipo de resposta = 300 | ✅ 265+20+15 |
| Metadados obrigatórios de item definidos | ✅ componente, unidade, objetivo, Bloom, dificuldade, fonte, revisor, status, versão |
| Limites legais citados com artigo | ✅ COFEN 736/2024, arts. 6º–8º; Lei 7.498/86 |
| Nenhuma atribuição privativa ao técnico | ✅ após A1 e M1 |
| Fontes com data e licença | ✅ 18 fontes registradas |
| Nenhum dado real de paciente | ✅ casos fictícios |

---

## 8. Pendências que exigem revisor humano

1. **Validação técnica por enfermeiro com registro ativo** — obrigatória antes de publicar
   (PRD, 11.2). Itens a revisar com prioridade: U5 (neurológico), U6 (endócrino), U7
   (cardiovascular) e U11 (reprodutor).
2. **Revisão pedagógica por educador** — nível de leitura para 16–18 anos e carga de 12 unidades.
3. **Faixas de referência de sinais vitais** — adotar **uma** fonte por parâmetro (idade e
   contexto) e citá-la; hoje o artefato fala em "faixas de referência" sem fixá-las.
4. **Escala de consciência** — confirmar se a instituição adota Glasgow ou outra escala
   padronizada antes de produzir os itens de U5.
5. **Edições exatas** de F8–F12 e link oficial de F17 (CNCT 2024).
6. **Decisão comercial** — define se a licença CC BY-NC-SA da OpenStax pode ser usada.
7. **Teste de acessibilidade** dos materiais M1/M2 com leitor de tela.

---

## 9. Parecer

Os artefatos da matéria 01 estão **tecnicamente coerentes e pedagogicamente alinhados** após as
correções A1, A2, A3, A5 e M1–M7. Não há erro factual identificado no conteúdo de anatomia e
fisiologia em si; os problemas estavam em **limites de escopo profissional**, **coerência
aritmética entre documentos** e **conformidade de licença**.

O status permanece **🟪 Em revisão** porque as pendências da seção 8 exigem profissionais
habilitados. Nenhuma publicação deve ocorrer antes disso.

**Próximo passo sugerido:** revisão técnica por enfermeiro, usando a seção 8 como roteiro, e
depois produção dos 300 itens.
