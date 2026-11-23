# 📱 RM-03 — Responsividade como Garantia Operacional

**Status:** vigente · **Versão:** 1.0 · **Vigência:** imediata a partir de **2026-11-23**
**Aprovação:** decisão da direção do projeto
**Abrangência:** site e aplicação do novo projeto (`enfermagem.appservice.tec.br`), mockups,
materiais HTML, telas administrativas e qualquer interface publicada

> **Regra de base e imutável.** A responsividade não é um recurso opcional nem um polimento de
> fim de projeto: é a **capacidade do site ou aplicação de ajustar layout, textos e imagens
> automaticamente ao tamanho da tela** e, por isso, é tratada como **máxima garantia operacional
> do projeto**. Nenhuma tela é considerada pronta fora desta regra.

---

## 1. A regra

| ID | Regra | Consequência |
|:--:|-------|--------------|
| **R1** | Toda tela publicada se adapta sozinha ao tamanho da tela | Sem versão "desktop" e "mobile" separadas, sem tela quebrada em nenhuma largura |
| **R2** | Layout, **texto e imagem** se ajustam juntos | Texto não é cortado, imagem não estoura o container, tabela não força rolagem lateral |
| **R3** | A responsividade é **critério de aceite**, não melhoria futura | Feature sem comportamento responsivo verificado não é entregue |

## 2. Requisitos mínimos (mensuráveis)

| # | Requisito | Medida |
|:-:|-----------|--------|
| M1 | Sem rolagem horizontal em nenhuma tela | `document.documentElement.scrollWidth <= innerWidth` em todas as larguras da matriz |
| M2 | Larguras de teste obrigatórias | **320, 360, 390, 414, 768, 1024, 1280, 1920** px |
| M3 | Área de toque | ≥ **44 × 44 px** em controles interativos |
| M4 | Tipografia legível | corpo ≥ 16 px; entrelinha ≥ 1.5; sem texto em imagem como única fonte |
| M5 | Zoom e reflow | utilizável a **200%** de zoom e com reflow a 320 px |
| M6 | Imagem responsiva | `max-width: 100%`, proporção preservada, `srcset`/`sizes` quando houver variantes, `alt` descritivo |
| M7 | Tabela e formulário | tabela adapta (scroll contido ou empilhamento); campo ocupa a largura do container |
| M8 | Sem sobreposição | nenhum elemento fixo cobre conteúdo ou ação em telas pequenas |
| M9 | Estado de carregamento | sem salto de layout (CLS) por imagem, fonte ou bloco assíncrono |
| M10 | Teclado e leitor de tela | foco visível, ordem lógica e rótulo em todo controle, em qualquer largura |

## 3. Como implementar

1. **Mobile-first:** CSS base para telas pequenas; `min-width` para ampliar.
2. **Unidades flexíveis:** `rem`, `%`, `fr`, `clamp()`; evitar largura fixa em pixel para blocos
   de conteúdo.
3. **Layout fluido:** Grid/Flex com `minmax()`, `auto-fit`; imagens e mídia com `max-width`.
4. **Breakpoints por conteúdo:** criar ponto de quebra quando o conteúdo quebra — não por marca
   de aparelho.
5. **Texto que cresce:** títulos com `clamp()`, sem `font-size` fixo em px para corpo.
6. **Tabelas:** contêiner com rolagem própria ou transformação em cartões em telas estreitas.
7. **Menu e navegação:** alvo de toque adequado; navegação não depende de hover.
8. **Modais e avisos:** altura máxima relativa à viewport, com rolagem interna.
9. **Formulários:** `input` com `font-size ≥ 16px` (evita zoom automático no iOS), teclado
   adequado (`type`, `inputmode`, `autocomplete`).
10. **Imagens:** dimensões declaradas, formato moderno, texto alternativo.

## 4. Verificação obrigatória (o portão)

Nenhuma entrega responsiva é aceita sem:

1. **Teste nas 8 larguras** da matriz M2, com evidência (captura ou registro do teste).
2. **Checagem de `scrollWidth`** em cada largura (M1).
3. **Teste de zoom a 200%** e de reflow a 320 px.
4. **Teste de teclado** (percorrer a tela só com Tab/Shift+Tab).
5. **Registro da evidência** no artefato da entrega (data, larguras, resultado).
6. **Revisão visual humana** em pelo menos um celular real ou emulador de viewport.

Enquanto a verificação não estiver registrada, o estado máximo da entrega é 🟪 (RM-01).

## 5. Proibições

1. Publicar tela com rolagem horizontal.
2. "Resolver" tela pequena escondendo conteúdo essencial.
3. Usar texto dentro de imagem sem alternativa textual.
4. Fixar largura mínima maior que 320 px.
5. Deixar botão ou link menor que 44 px de área de toque.
6. Depender de hover para revelar ação ou informação.
7. Entregar PDF como único formato de um conteúdo que precisa ser lido no celular.
8. Declarar "responsivo" sem teste registrado.

## 6. Aplicação ao estado atual

| Entrega | Situação |
|---------|----------|
| Mock `mockups/index.html` | Layout responsivo com breakpoints em 980 px e 640 px; **revisão nas 8 larguras pendente** |
| Sistema atual (EN_430) | Fora do escopo desta regra até a migração para o novo projeto |

## 7. Controle do documento

| Versão | Data | Autor | Alteração |
|:------:|------|-------|-----------|
| 1.0 | 2026-11-23 | Direção do projeto | Criação da regra de responsividade como garantia operacional do novo projeto |

*Regra de base imutável: complementável, não revogável.*
