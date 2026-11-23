# Proposta de Itens — 04 Políticas Públicas de Saúde e SUS

**Total:** 250 itens únicos · **Status:** especificação 🟪 (revisão humana pendente)
**Regra:** nenhum item pode exigir decisão de gestão, orçamento ou regulação; o foco é
reconhecer o direito, o fluxo e a vigência da norma (RM-02 §4; ficha §6).

---

## 1. Distribuição por unidade

| # | Unidade | Itens | % |
|:-:|---------|:-----:|:-:|
| 1 | História e Reforma Sanitária | 22 | 8,8% |
| 2 | Direito e seguridade social | 20 | 8,0% |
| 3 | Lei 8.080/1990 | 30 | 12,0% |
| 4 | Controle social | 22 | 8,8% |
| 5 | Princípios do SUS | 30 | 12,0% |
| 6 | Níveis e RAS | 28 | 11,2% |
| 7 | APS, ESF e território | 34 | 13,6% |
| 8 | Programas e redes | 24 | 9,6% |
| 9 | Financiamento | 22 | 8,8% |
| 10 | Vigilância e notificação | 18 | 7,2% |
| | **Total** | **250** | **100%** |

Peso maior em U7 (prática do técnico), U3 e U5 (base legal que sustenta o restante) e U6 (fluxo).

## 2. Distribuição por nível de Bloom

| Bloom | Itens | % |
|-------|:-----:|:-:|
| Lembrar | 70 | 28% |
| Entender | 85 | 34% |
| Aplicar | 65 | 26% |
| Analisar | 30 | 12% |
| **Aplicar + Analisar** | **95** | **38%** ✅ (meta ≥ 30%) |

Fecha com a progressão de `matriz_competencias.md` (blocos A/B/C = 94/92/64).

## 3. Distribuição por dificuldade

| Dificuldade | Itens | % | Critério |
|-------------|:-----:|:-:|----------|
| Fácil | 90 | 36% | Conceito, marco legal ou definição direta |
| Média | 110 | 44% | Aplicação a caso curto ou fluxo |
| Difícil | 50 | 20% | Integração de duas normas, transição ou distrator próximo |

## 4. Distribuição por tipo de item

**Por tipo de resposta** (eixos exclusivos, somam 250):

| Tipo | Itens | Formato |
|------|:-----:|---------|
| Múltipla escolha (A–D) | 180 | Uma correta inequívoca, 3 distratores plausíveis |
| Associação de colunas | 30 | Princípio ↔ definição; serviço ↔ nível; evento ↔ prazo |
| Ordenação | 20 | Fluxo na RAS, cadeia de notificação, linha do tempo |
| Caso com resposta curta | 20 | Situação fictícia + justificativa em 2–3 linhas |

**Por estímulo** (recorte transversal — não somar):

| Estímulo | Itens | Descrição |
|----------|:-----:|-----------|
| Texto puro | 170 | Enunciado conceitual ou de aplicação |
| Caso curto | 60 | Usuário fictício em uma unidade do SUS |
| Quadro/mapa | 20 | Fluxo, tabela de repasses ou linha do tempo |

## 5. Metadados obrigatórios por item

```yaml
componente: "04"
unidade: U9
objetivo: "Identificar o modelo de cofinanciamento federal da APS vigente"
bloom: aplicar
dificuldade: media
tipo: multipla_escolha
fonte: F14            # Portaria GM/MS 3.493/2024
norma_versao: "3.493/2024, alterada pela 7.799/2025 e pela 10.994/2026; consulta 2026-11-23"
revisor: <enfermeiro com registro ativo>
status: rascunho | revisado | publicado
versao: 1
```

`norma_versao` é obrigatório: o item pode continuar correto quando a portaria muda, mas o
comentário precisa dizer qual versão foi usada.

## 6. Exemplos por nível

### Exemplo 1 — Lembrar (fácil) · U2

**Enunciado:** A Constituição de 1988 estabelece, no art. 196, que a saúde é
A) direito de todos e dever do Estado.
B) benefício apenas de quem contribui para a previdência.
C) serviço restrito a trabalhadores com carteira assinada.
D) assistência voluntária oferecida por entidades filantrópicas.

**Gabarito:** A
**Feedback:** O art. 196 universaliza o direito e atribui ao Estado o dever de garantir as políticas
que o assegurem. Contribuição é critério da previdência, não da saúde.
**Fonte:** F1.

### Exemplo 2 — Entender (fácil) · U5

**Enunciado:** A equidade, um dos princípios do SUS, significa
A) oferecer exatamente o mesmo atendimento a todos, sem distinção.
B) tratar de forma diferente quem tem necessidades diferentes, para reduzir desigualdades.
C) atender primeiro quem chegou primeiro, independentemente do risco.
D) reservar vagas para grupos com plano de saúde.

**Gabarito:** B
**Feedback:** Equidade é justiça distributiva: reconhece diferenças e prioriza quem mais precisa.
Igualdade formal não corrige desigualdade de acesso.
**Fonte:** F2.

### Exemplo 3 — Aplicar (média) · U7

**Enunciado:** Em uma UBS, um usuário com febre alta e tosse procura atendimento sem agendamento.
O técnico de enfermagem deve, prioritariamente,
A) encaminhar imediatamente ao hospital de alta complexidade sem avaliação.
B) acolher, verificar sinais vitais, aplicar o protocolo de risco e comunicar o enfermeiro.
C) orientar retorno em 7 dias, pois não há consulta no dia.
D) prescrever antitérmico conforme a queixa.

**Gabarito:** B
**Feedback:** A APS trabalha com acolhimento e classificação de risco; o técnico acolhe, verifica,
registra e comunica. Prescrição e definição de conduta são do enfermeiro.
**Fonte:** F11, F22.

### Exemplo 4 — Analisar (difícil) · U9

**Enunciado:** Um material didático de 2023 afirma que o financiamento da APS se dá pelo Previne
Brasil, com base em indicadores de desempenho. Em 2026, essa afirmação está
A) correta, pois o Previne Brasil continua sendo o único modelo.
B) desatualizada, pois o Previne Brasil foi substituído pela Portaria 3.493/2024, alterada pela
   7.799/2025, com transição prorrogada pela 10.994/2026.
C) correta apenas para municípios que não aderiram ao novo modelo.
D) incorreta, pois a APS passou a ser financiada apenas por emendas parlamentares.

**Gabarito:** B
**Feedback:** O Previne Brasil foi substituído; o modelo atual tem componentes de equidade, vínculo
e acompanhamento territorial, e qualidade. A transição foi prorrogada, mas o modelo antigo não é
mais a referência vigente.
**Fonte:** F14–F16.

### Exemplo 5 — Analisar (difícil) · U10

**Enunciado:** Um serviço atende um caso suspeito de febre do Oropouche em gestante. Segundo a
lista nacional atualizada em 2026, a conduta de notificação é
A) notificação semanal, como qualquer caso.
B) notificação imediata à SMS, à SES e ao MS.
C) dispensar notificação até confirmação laboratorial.
D) notificar apenas se houver óbito.

**Gabarito:** B
**Feedback:** A Portaria 11.211/2026 incluiu a febre do Oropouche: casos são semanais, mas óbitos,
óbitos fetais, gestantes e anomalias congênitas exigem notificação imediata. A notificação não
espera confirmação quando a norma exige imediata.
**Fonte:** F19, F18.

## 7. Regras de qualidade para a produção

1. Enunciado com contexto suficiente para responder sem adivinhar.
2. Sem dupla negação, "exceto" ou pegadinha.
3. Distratores derivados de erros reais (mapa de unidades, seção "erro comum").
4. Alternativas homogêneas em extensão e gramática.
5. Feedback explica o raciocínio e cita a fonte e a versão da norma.
6. Item de financiamento sempre indica portaria e ano; item de programa sempre indica vigência.
7. Nenhum item usa dados reais de usuário ou de território identificável.
8. Cada item tem revisor humano identificado.

## 8. Pendências

- [ ] Produzir os 250 itens conforme a especificação.
- [ ] Revisão técnica (enfermeiro) e pedagógica.
- [ ] Confirmar a lista de notificação após a Portaria 12.508/2026.
- [ ] Piloto com 1 turma: p-value e discriminação.
- [ ] Atualizar os itens de U9 se a transição do cofinanciamento mudar em 2027.
