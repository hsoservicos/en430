# Proposta de Materiais — 01 Anatomia e Fisiologia Humana

**Objetivo:** cobrir a matéria com poucos materiais bem feitos, reutilizáveis no app (PWA) e na
impressão. Meta de referência do projeto: ~88 materiais para 17 componentes; aqui se propõem 6.

---

## 1. Catálogo

| # | Material | Formato | Público | Cobre | Esforço |
|:-:|----------|---------|---------|-------|:-------:|
| M1 | **Guia de bolso dos 11 sistemas** | Markdown → HTML/PDF | Estudante | U1–U11 | Médio |
| M2 | **Mapa integrado do corpo** | Infográfico (1 página) | Estudante | U1–U12 | Alto |
| M3 | **Deck de flashcards (120 cartões)** | App / Anki-like | Estudante | U1–U12 | Médio |
| M4 | **Casos-âncora (6 casos)** | Markdown + feedback | Estudante | U5–U12 | Alto |
| M5 | **Roteiro de laboratório "localizar e descrever"** | Checklist | Professor | U3–U4, U7–U10 | Baixo |
| M6 | **Glossário termo técnico ↔ linguagem simples** | Markdown | Estudante | Todas | Baixo |

---

## 2. Especificação de cada material

### M1 — Guia de bolso dos 11 sistemas

- **Estrutura por sistema:** o que é → como funciona em 5 linhas → onde aparece na prática →
  3 sinais que o técnico observa → 1 erro comum → referência.
- **Extensão:** 2 páginas por sistema (22 páginas).
- **Regra:** nenhum sistema ultrapassa 6 conceitos-chave.
- **Acessibilidade:** títulos hierárquicos, tabelas com cabeçalho, sem texto em imagem.

### M2 — Mapa integrado do corpo

- Uma única ilustração com os sistemas e os parâmetros que cada um gera (PA, FR, SpO2, diurese,
  glicemia, temperatura, dor).
- **Licença:** ilustração própria ou banco com licença compatível; **não** reutilizar figura de
  livro protegido nem da OpenStax se o produto for comercial.
- **Versão textual obrigatória** para leitor de tela.

### M3 — Deck de flashcards (120 cartões)

| Bloco | Cartões | Exemplo de frente/verso |
|-------|:-------:|-------------------------|
| Terminologia e planos | 15 | "Proximal" ↔ "Mais próximo da raiz do membro" |
| Sistemas e funções | 40 | "Função do sistema tegumentar" ↔ barreira, sensibilidade, termorregulação |
| Estruturas-chave | 35 | "Néfron" ↔ unidade funcional do rim |
| Parâmetro ↔ sistema | 20 | "SpO2" ↔ oxigenação, sistema respiratório |
| Erros comuns | 10 | "Veia transporta sangue arterial?" ↔ não necessariamente |

- **Algoritmo:** SM-2 (ou equivalente) com revisão diária.
- **Regra:** 1 conceito por cartão; sem cartão de lista longa.

### M4 — Casos-âncora (6 casos)

| Caso | Tema | Sistemas integrados | Bloom |
|------|------|---------------------|-------|
| C1 | Sr. A., 62 anos, ICC descompensada | Cardiovascular, respiratório, urinário | Aplicar |
| C2 | Sra. B., 28 anos, pós-operatório | Tegumentar, muscular, cardiovascular | Aplicar |
| C3 | Jovem C., 17 anos, crise asmática | Respiratório, muscular | Aplicar |
| C4 | Sra. D., 74 anos, desidratação por diarreia | Digestório, urinário, cardiovascular | Analisar |
| C5 | Sr. E., 55 anos, AVE isquêmico | Nervoso, muscular, cardiovascular | Analisar |
| C6 | Sra. F., 34 anos, hipoglicemia | Endócrino, nervoso | Analisar |

Cada caso: apresentação fictícia → 3 perguntas de complexidade crescente → feedback com fonte →
"o que o técnico comunica ao enfermeiro".

### M5 — Roteiro de laboratório

- 8 estações: pulsos, ausculta, proeminências ósseas, articulações, posicionamento, sítios de
  injeção (identificação em manequim), pontos de ausculta respiratória, palpação abdominal.
- **Rubrica:** insuficiente / em desenvolvimento / competente.
- **Segurança:** sem procedimento invasivo em colega.

### M6 — Glossário

- 80–120 termos, ordem alfabética, com equivalente simples e exemplo de uso.
- Reaproveitável por todas as matérias do curso (entra no glossário global).

---

## 3. Tempo de estudo, versão e licença

| Material | Tempo estimado | Licença prevista | Versão inicial |
|----------|:--------------:|------------------|:--------------:|
| M1 | 3h de leitura (12 blocos de 15 min) | CC BY-NC-SA 4.0 (a confirmar com jurídico) | 1.0 |
| M2 | 20 min | própria (ilustração encomendada) | 1.0 |
| M3 | 10 min/dia em repetição espaçada | própria | 1.0 |
| M4 | 30 min por caso | própria | 1.0 |
| M5 | 2h de laboratório | própria | 1.0 |
| M6 | consulta contínua | própria | 1.0 |

**Total estimado de estudo orientado:** ~12h de material estruturado para 60h de componente —
as 48h restantes são aula, prática e avaliação. O plano semanal (PRD, FR-15) usa esses tempos
para montar a agenda.

## 4. Relação com o sistema atual

| Material | Reaproveita do banco atual? | Ação |
|----------|-----------------------------|------|
| M1 | Parcialmente (módulos 3 e 6) | Reescrever com o recorte desta pesquisa |
| M2 | Não | Produzir do zero |
| M3 | Não | Produzir novo; integrar ao motor de repetição espaçada |
| M4 | Não | Produzir novo; casos fictícios |
| M5 | Não | Produzir com professor |
| M6 | Parcialmente | Consolidar no glossário global |

## 5. Critérios de aceite

- [ ] Todo material tem fonte e data de revisão.
- [ ] Toda figura tem licença registrada e `alt`.
- [ ] Banco `imagens/` usado, creditado e sem figura fora do manifesto (RM-04).
- [ ] Nenhum material usa dados reais de paciente.
- [ ] Linguagem verificada para leitura por estudante de nível médio.
- [ ] Versão acessível (HTML) além do PDF.
- [ ] Duas revisões: técnica (enfermeiro) e pedagógica.
