# Proposta de Itens — 01 Anatomia e Fisiologia Humana

**Total:** 300 itens únicos · **Status:** especificação aprovada para produção
**Regra:** nenhum item de anatomia pode exigir diagnóstico ou prescrição.

---

## 1. Distribuição por unidade

| # | Unidade | Itens | % |
|:-:|---------|:-----:|:-:|
| 1 | Organização do corpo humano | 24 | 8% |
| 2 | Sistema tegumentar | 20 | 7% |
| 3 | Sistema esquelético | 26 | 9% |
| 4 | Sistema muscular | 22 | 7% |
| 5 | Sistema nervoso | 30 | 10% |
| 6 | Sistema endócrino | 26 | 9% |
| 7 | Sistema cardiovascular | 40 | 13% |
| 8 | Sistema respiratório | 32 | 11% |
| 9 | Sistema digestório | 26 | 9% |
| 10 | Sistema urinário | 28 | 9% |
| 11 | Sistema reprodutor | 18 | 6% |
| 12 | Integração e homeostase | 8 | 3% |
| | **Total** | **300** | **100%** |

Peso maior em cardiovascular e respiratório porque concentram os parâmetros mais verificados
pelo técnico (PA, pulso, frequência respiratória, SpO2).

## 2. Distribuição por nível de Bloom

| Bloom | Itens | % | Meta do PRD |
|-------|:-----:|:-:|-------------|
| Lembrar | 88 | 29% | — |
| Entender | 104 | 35% | — |
| Aplicar | 82 | 27% | — |
| Analisar | 26 | 9% | — |
| **Aplicar + Analisar** | **108** | **36%** | ≥ 30% ✅ |

A distribuição não é arbitrária: deriva da progressão cognitiva por bloco de unidades
definida em `matriz_competencias.md` (92 itens nas unidades 1–4, 154 nas 5–9 e 54 nas 10–12).

## 3. Distribuição por dificuldade

| Dificuldade | Itens | % | Critério |
|-------------|:-----:|:-:|----------|
| Fácil | 120 | 40% | Definição ou identificação direta |
| Média | 135 | 45% | Correlação estrutura–função ou caso curto |
| Difícil | 45 | 15% | Integração de dois sistemas ou distratores próximos |

## 4. Distribuição por tipo de item

**Por tipo de resposta** (eixos mutuamente exclusivos, somam 300):

| Tipo | Itens | Formato |
|------|:-----:|---------|
| Múltipla escolha (A–D) | 265 | Uma correta inequívoca, 3 distratores plausíveis |
| Associação de colunas | 20 | Sistema ↔ função; estrutura ↔ localização |
| Ordenação | 15 | Sequência de fluxo (ex.: sangue, ar, alimento, urina) |

**Por estímulo** (recorte transversal dos 265 itens de múltipla escolha — não somar à tabela acima):

| Estímulo | Itens | Descrição |
|----------|:-----:|-----------|
| Texto puro | 240 | Enunciado conceitual ou de correlação |
| Caso clínico curto | 15 | Situação fictícia + 1 pergunta de aplicação |
| Figura | 10 | Ilustração própria com `alt` e pergunta de identificação |

## 5. Metadados obrigatórios por item

```yaml
componente: "01"
unidade: U7
objetivo: "Interpretar pulso e PA frente a faixas de referência"
bloom: aplicar
dificuldade: media
tipo: multipla_escolha
fonte: F11
autor: <nome>
revisor: <enfermeiro com registro ativo>
status: rascunho | revisado | publicado
versao: 1
```

## 6. Exemplos de itens por nível

### Exemplo 1 — Lembrar (fácil)

**Enunciado:** Qual estrutura separa o tórax do abdome?
A) Pleura
B) Diafragma
C) Peritônio
D) Mediastino

**Gabarito:** B
**Feedback:** O diafragma é o músculo em cúpula que separa as cavidades torácica e abdominal e
participa da inspiração. A pleura reveste os pulmões; o peritônio, a cavidade abdominal; o
mediastino é o espaço entre os pulmões.
**Unidade:** U1 · **Fonte:** F11

### Exemplo 2 — Entender (média)

**Enunciado:** A insulina reduz a glicemia porque promove, principalmente,
A) a quebra de glicogênio no fígado.
B) a entrada de glicose nas células.
C) a absorção de glicose no intestino.
D) a produção de glucagon pelo pâncreas.

**Gabarito:** B
**Feedback:** A insulina facilita o transporte de glicose para dentro das células e favorece o
armazenamento. A quebra de glicogênio e a produção de glucagon têm efeito oposto (aumentam a
glicemia).
**Unidade:** U6 · **Fonte:** F10

### Exemplo 3 — Aplicar (média)

**Enunciado:** Durante a verificação de sinais vitais, o técnico encontra PA 88/54 mmHg, pulso
118 bpm, pele fria e sudorética em um paciente adulto. Esses achados são mais compatíveis com
A) hipertensão arterial.
B) redução da perfusão tecidual.
C) aumento do retorno venoso.
D) bradicardia com vasodilatação.

**Gabarito:** B
**Feedback:** Pressão baixa, taquicardia compensatória e pele fria indicam má perfusão. O técnico
deve comunicar imediatamente ao enfermeiro, sem formular diagnóstico (COFEN 736/2024, art. 6º).
**Unidade:** U7 · **Fonte:** F10, F11

### Exemplo 4 — Analisar (difícil)

**Enunciado:** Um paciente com diarreia intensa apresenta fraqueza, pulso rápido e filiforme e
redução do volume urinário. A explicação que integra corretamente os achados é:
A) queda do volume circulante, com redução da filtração renal e taquicardia compensatória.
B) aumento da absorção de água, com elevação da pressão e redução da diurese.
C) aumento da filtração glomerular por sobrecarga hídrica, com queda da diurese.
D) aumento do volume sistólico por hiperidratação, com pulso filiforme.

**Gabarito:** A
**Feedback:** A perda de líquido reduz o volume circulante, cai a pressão de perfusão e o rim
retém água, enquanto a frequência cardíaca sobe como compensação. As demais alternativas
invertem a relação causa-efeito.
**Unidade:** U9 + U10 + U7 · **Fonte:** F10

## 7. Regras de qualidade para a produção

1. Enunciado com contexto suficiente para responder sem adivinhar.
2. Sem dupla negação, "exceto", "incorreto" e pegadinha.
3. Distratores derivados de erros conceituais reais (registrados na ficha da matéria).
4. Alternativas homogêneas em extensão e gramática.
5. Feedback explica o raciocínio e cita a fonte.
6. Item com figura só se a figura for indispensável.
7. Nenhum item usa dados de paciente real.
8. Cada item tem revisor humano identificado.

## 8. Pendências

- [ ] Produzir os 300 itens conforme esta especificação.
- [ ] Revisão técnica por enfermeiro e pedagógica.
- [ ] Piloto com 1 turma e análise de p-value e discriminação.
- [ ] Produção das 10 figuras próprias licenciadas.
