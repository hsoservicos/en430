# Proposta de Itens — 03 Biossegurança e Controle de Infecção (CCIH)

**Total:** 200 itens únicos · **Status:** especificação 🟪 (revisão humana pendente)
**Regra:** nenhum item pode exigir prescrição, definição de isolamento ou validação de
esterilização (RM-02 §4; matriz §4).

---

## 1. Distribuição por unidade

| # | Unidade | Itens | % |
|:-:|---------|:-----:|:-:|
| 1 | Biossegurança e riscos | 20 | 10% |
| 2 | Higiene das mãos | 22 | 11% |
| 3 | Precauções padrão | 20 | 10% |
| 4 | Precauções por transmissão | 22 | 11% |
| 5 | EPI e EPC | 24 | 12% |
| 6 | Processamento e CME | 22 | 11% |
| 7 | PGRSS | 20 | 10% |
| 8 | Segurança do paciente e NSP | 20 | 10% |
| 9 | CCIH e vigilância | 14 | 7% |
| 10 | Acidente com material biológico | 16 | 8% |
| | **Total** | **200** | **100%** |

Peso maior em U5 e U2/U4/U6 porque concentram técnica e decisão que o técnico executa sozinho
no plantão.

## 2. Distribuição por nível de Bloom

| Bloom | Itens | % |
|-------|:-----:|:-:|
| Lembrar | 58 | 29% |
| Entender | 68 | 34% |
| Aplicar | 56 | 28% |
| Analisar | 18 | 9% |
| **Aplicar + Analisar** | **74** | **37%** ✅ (meta ≥ 30%) |

Fecha com a tabela de progressão de `matriz_competencias.md` (blocos A/B/C = 62/88/50).

## 3. Distribuição por dificuldade

| Dificuldade | Itens | % | Critério |
|-------------|:-----:|:-:|----------|
| Fácil | 80 | 40% | Conceito ou identificação direta |
| Média | 90 | 45% | Aplicação a um caso curto |
| Difícil | 30 | 15% | Integração de duas normas ou distratores próximos |

## 4. Distribuição por tipo de item

**Por tipo de resposta** (eixos exclusivos, somam 200):

| Tipo | Itens | Formato |
|------|:-----:|---------|
| Múltipla escolha (A–D) | 180 | Uma correta inequívoca, 3 distratores plausíveis |
| Associação de colunas | 12 | Resíduo ↔ grupo; artigo ↔ classe |
| Ordenação | 8 | Sequência de paramentação, retirada, fluxo da CME |

**Por estímulo** (recorte transversal dos 180 itens de múltipla escolha — não somar):

| Estímulo | Itens | Descrição |
|----------|:-----:|-----------|
| Texto puro | 156 | Enunciado conceitual ou de aplicação |
| Caso clínico curto | 18 | Situação fictícia de unidade de internação |
| Figura | 6 | Cartaz/sinalização própria, com `alt` e pergunta de interpretação |

## 5. Metadados obrigatórios por item

```yaml
componente: "03"
unidade: U7
objetivo: "Segregar resíduo no ponto de geração conforme o grupo"
bloom: aplicar
dificuldade: media
tipo: multipla_escolha
fonte: F5            # RDC 222/2018
norma_versao: "RDC 222/2018, consultada em 2026-11-23"
revisor: <enfermeiro com registro ativo>
status: rascunho | revisado | publicado
versao: 1
```

`norma_versao` é obrigatório nesta matéria: a fonte pode mudar sem alterar o item.

## 6. Exemplos por nível

### Exemplo 1 — Lembrar (fácil) · U2

**Enunciado:** Segundo os cinco momentos da OMS, a higiene das mãos deve ser feita antes de tocar
o paciente com o objetivo principal de
A) proteger o profissional.
B) proteger o paciente.
C) limpar a luva.
D) reduzir o uso de álcool.

**Gabarito:** B
**Feedback:** Os dois primeiros momentos protegem o paciente; os três seguintes protegem o
profissional e o ambiente. Higiene das mãos não substitui nem limpa luva.
**Fonte:** F12, F13.

### Exemplo 2 — Entender (média) · U6

**Enunciado:** Um artigo que entra em contato com mucosa íntegra é classificado como semicrítico.
O processamento mínimo esperado é
A) limpeza com água e sabão.
B) desinfecção ou esterilização, conforme o uso.
C) apenas secagem com pano limpo.
D) guarda em armário fechado sem processamento.

**Gabarito:** B
**Feedback:** Artigo semicrítico exige, no mínimo, desinfecção; esterilização é indicada quando o
uso exigir. Limpeza é etapa obrigatória, mas não é processamento suficiente.
**Fonte:** F6 (conferir no texto da RDC 15/2012 na revisão).

### Exemplo 3 — Aplicar (média) · U7

**Enunciado:** No descarte de uma seringa usada na administração de medicamento, o técnico deve
A) reencapar a agulha e descartar no lixo comum.
B) descartar a agulha no coletor de perfurocortante, sem reencapar.
C) descartar tudo no saco branco leitoso.
D) separar a agulha e guardá-la para conferência.

**Gabarito:** B
**Feedback:** Perfurocortante vai para coletor rígido, sem reencape, para reduzir acidente. O
resíduo do medicamento pode ter classificação química e deve ser avaliado à parte.
**Fonte:** F4, F5.

### Exemplo 4 — Analisar (difícil) · U4 + U10

**Enunciado:** Um técnico sofreu exposição percutânea durante punção e, ao final do plantão,
comunicou o fato ao colega. Considerando a NR-32 e o fluxo de segurança do paciente, o erro
principal foi
A) usar luva durante o procedimento.
B) descartar o material em coletor apropriado.
C) não comunicar imediatamente e não registrar o acidente.
D) trocar de luva entre pacientes.

**Gabarito:** C
**Feedback:** A comunicação imediata permite avaliação e encaminhamento; o registro (incluindo
CAT quando cabível) é obrigatório. Luva e descarte correto não são erros.
**Fonte:** F4, F7, F9.

## 7. Regras de qualidade para a produção

1. Enunciado com contexto suficiente para responder sem adivinhar.
2. Sem dupla negação, "exceto" ou pegadinha.
3. Distratores derivados de erros reais (mapa de unidades, seção "erro comum").
4. Alternativas homogêneas em extensão e gramática.
5. Feedback explica o raciocínio e cita a fonte e a versão da norma.
6. Item com figura só se a figura for indispensável; sempre com alternativa textual.
7. Nenhum item usa dados de paciente real.
8. Cada item tem revisor humano identificado.

## 8. Pendências

- [ ] Produzir os 200 itens conforme a especificação.
- [ ] Revisão técnica (enfermeiro) e pedagógica.
- [ ] Piloto com 1 turma: p-value e discriminação.
- [ ] Produzir as 6 figuras próprias (sinalização e cartaz) licenciadas para o projeto.
