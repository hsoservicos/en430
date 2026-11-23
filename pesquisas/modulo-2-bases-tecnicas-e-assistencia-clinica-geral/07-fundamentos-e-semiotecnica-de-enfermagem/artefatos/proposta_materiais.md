# Proposta de Materiais — 07 Fundamentos e Semiotécnica de Enfermagem

**CH:** 120h · **Status:** 🟪 em revisão
**Princípio:** técnica se aprende **fazendo e sendo observado**. O material é checklist de execução,
não resumo para decorar.

---

## 1. Inventário por unidade

| # | Unidade | Material principal | Apoio |
|:-:|---------|--------------------|-------|
| 1 | Processo e registros | M1 — Caderno de anotação modelo | Glossário de termos de registro |
| 2 | Sinais vitais | M2 — Cartão de técnica e faixas | Ficha de registro de SSVV |
| 3 | Exame físico | M3 — Roteiro céfalo-podálico | Banco de achados descritivos |
| 4 | Antropometria | M4 — Ficha de medidas | Calculadora de IMC (offline) |
| 5 | Higiene e conforto | Checklist de banho no leito | Roteiro de conforto ambiental |
| 6 | Mobilização | Checklist de transferência | Cartaz de biomecânica |
| 7 | Lesão por pressão | M5 — Caso + plano preventivo | Escalas de risco comparadas |
| 8 | Oxigenoterapia | Checklist de montagem | Tabela dispositivo → fluxo |
| 9 | Sondagens | Checklist de auxílio e cuidados | Cartaz de fixação e complicações |
| 10 | Curativos | M6 — Ficha de descrição de ferida | Matriz cobertura → indicação |
| 11 | Coleta | Checklist de coleta e identificação | Quadro de erros de coleta |
| 12 | Segurança | M7 — Cenário de 3 falhas | Cartaz das metas |

## 2. Especificação dos materiais

### M1 — Caderno de anotação modelo (U1)
- 6 anotações-modelo: uma correta, cinco com defeito (sem dado, com interpretação, sem horário, com
  prescrição, sem comunicação registrada).
- Tarefa: corrigir cada uma e justificar em uma frase.
- Regra: nunca reproduzir prontuário real.

### M2 — Cartão de técnica e faixas (U2)
- Frente: técnica em 6 passos por sinal, com o erro comum.
- Verso: faixas de referência **com a fonte e o ano** ao lado de cada valor.
- Tarja de aviso: "faixa é referência; decisão é do enfermeiro".

### M3 — Roteiro céfalo-podálico (U3)
- 12 segmentos com "o que inspecionar", "o que palpar" e "como registrar".
- Coluna de separação: **achado** × **interpretação** (o erro mais frequente).

### M4 — Ficha de medidas (U4)
- Peso, altura, IMC, circunferência abdominal e perímetro cefálico, com condição da medida
  (roupa, horário, balança).
- Calculadora offline em HTML, com arredondamento declarado.

### M5 — Caso de LPP com plano preventivo (U7)
- Caso com Braden calculado, três fatores de risco e uma lesão já instalada.
- Tarefa: montar plano de 24h (superfície, decúbito, inspeção, registro) e dizer o que **não** faz.
- Gabarito comentado, com a diretriz citada.

### M6 — Ficha de descrição de ferida (U10)
- Campos: local, medida (cm), leito, borda, exsudato, pele perilesional, odor, dor, cobertura, data.
- Exemplo preenchido + exemplo vago ("ferida limpa, sem alterações") para comparação.

### M7 — Cenário de 3 falhas de segurança (U12)
- Plantão fictício com três falhas plantadas (identificação, checagem, higiene).
- Tarefa: apontar, classificar a meta violada e dizer o que fazer diferente.

## 3. Checklists (conteúdo mínimo)

### 3.1 Sinais vitais — itens eliminatórios
1. Higienizar as mãos antes e depois.
2. Conferir identificação do paciente.
3. Explicar o procedimento e posicionar corretamente.
4. Usar manguito de tamanho adequado, braço apoiado na altura do coração.
5. Registrar valor, unidade, local, horário e condição.
6. Comunicar alteração ao enfermeiro e registrar a comunicação.

### 3.2 Banho no leito — itens eliminatórios
1. Reunir material antes e testar a temperatura da água.
2. Preservar privacidade (biombo/porta) e descobrir só o segmento em cuidado.
3. Proteger dispositivos (acesso, sonda, dreno) durante a mobilização.
4. Inspecionar a pele, incluindo regiões de pressão.
5. Deixar o paciente seguro, seco, com roupa limpa e campainha ao alcance.

### 3.3 Curativo — itens eliminatórios
1. Conferir prescrição e tipo de cobertura.
2. Higienizar as mãos e usar técnica asséptica.
3. Remover a cobertura anterior sem arrastar sobre a ferida.
4. Limpar no sentido indicado e não retornar à área limpa.
5. Registrar medida, aspecto, exsudato e cobertura.
6. Descartar resíduo corretamente.

## 4. Rubrica de estação prática (3 níveis)

| Nível | Descrição | Consequência |
|-------|-----------|--------------|
| **Insuficiente** | Falha em item eliminatório ou executa sem higiene/identificação | Repetir a estação |
| **Adequado** | Executa todos os eliminatórios com supervisão próxima | Aprovado com recomendação |
| **Autônomo** | Executa com segurança e registra de forma completa | Aprovado |

## 5. Recursos visuais (RM-04)

- Banco `imagens/` com 7 imagens licenciadas: esfigmomanômetro, oxímetro, termômetro (38,7 °C),
  locais de LPP, colchão alternado, proteção de calcâneo e posição da sonda nasogástrica.
- Uso: M2 (instrumentos e leitura), M5 (locais de LPP e superfície de apoio), M6 (proteção de
  calcâneo), U9 (trajeto da sonda).
- Nenhuma imagem mostra paciente identificável; todas têm crédito e legenda.

## 6. Acessibilidade e responsividade

- Checklists em HTML semântico, com `fieldset`/`legend` e navegação por teclado.
- Formulários de registro: uma coluna no celular, duas no desktop, fonte ≥ 16 px, alvo ≥ 44 px.
- Imagens com `srcset` e `sizes`; legenda fora da imagem; `alt` em todos os gráficos e diagramas.
- Nenhuma informação transmitida só por cor (faixas e escalas usam rótulo + cor).
- PDFs gerados do HTML, com tags e ordem de leitura — não digitalizações.

## 7. Pendências

- [ ] Validar M2, M5 e M6 com enfermeiro revisor.
- [ ] Fotografar as estações no laboratório da instituição (com termo de imagem).
- [ ] Testar os formulários em 320, 360, 390, 414, 768, 1024, 1280, 1920 e 2560 px.
- [ ] Revisar faixas de referência depois de fixar F11 e F13.
