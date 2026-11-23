# Banco de imagens — 08 Farmacologia e Cálculo de Medicamentos

**Status:** 🟪 em revisão (licenças conferidas na origem em 2026-11-23) · **Ativos:** 4
**Regra:** [RM-04](../../../../docs/RM-04_ILUSTRACAO_E_RECURSOS_VISUAIS.md) · **Catálogo:** [`../../../RECURSOS_VISUAIS.md`](../../../RECURSOS_VISUAIS.md)

Origem: Wikimedia Commons, via `Special:FilePath` em 1200 px de largura. Cada arquivo tem licença,
autor e página de origem declarados. Imagem sem licença livre **não entra**.

> **Cuidado de conteúdo:** nenhuma imagem deste banco mostra medicamento real em paciente. São
> cenas de **dispositivo** e de **contexto de via**. Nome comercial, rótulo legível e dose não são
> usados como conteúdo avaliado.

---

## 1. Ativos

### `bomba_infusao.jpg`

| Campo | Valor |
|-------|-------|
| Unidade | U7 |
| `alt` | Bomba de infusão de seringa em uso, com seringa acoplada e painel de programação |
| Legenda | Bomba de infusão de seringa: a velocidade é programada e conferida, não estimada por observação |
| Licença | CC BY-SA 4.0 |
| Autor | Shyamal |
| Original | 3735x1826 px |
| SHA-256 (12) | `d0218ac65bb0` |
| Página | <https://commons.wikimedia.org/wiki/File:Syringe_infusion_pump.jpg> |
| Uso didático | Mostrar que a bomba substitui o cálculo manual de gotejamento, mas não a conferência |

### `regulador_fluxo.jpg`

| Campo | Valor |
|-------|-------|
| Unidade | U7 |
| `alt` | Regulador de fluxo (pinça de rolete) de equipo de infusão, em close |
| Legenda | Regulador de fluxo do equipo: pequena variação na abertura muda o gotejamento real |
| Licença | Public domain |
| Autor | Harmid |
| Original | 2560x1920 px |
| SHA-256 (12) | `5cf693363ba4` |
| Página | <https://commons.wikimedia.org/wiki/File:Debietregelaar_(2).jpg> |
| Uso didático | Explicar por que o cálculo deve ser conferido na beira do leito |

### `perfusao_montagem.jpg`

| Campo | Valor |
|-------|-------|
| Unidade | U11 |
| `alt` | Profissional preparando um dispositivo de perfusão com solução medicamentosa |
| Legenda | Preparo de perfusão: concentração, diluente e rótulo são conferidos antes de instalar |
| Licença | CC BY-SA 3.0 |
| Autor | Bpierreb |
| Original | 1504x3264 px |
| SHA-256 (12) | `d16983c68155` |
| Página | <https://commons.wikimedia.org/wiki/File:Diffuseur_m%C3%A9dical.jpg> |
| Uso didático | Abrir a unidade de preparo seguro e rotulagem |

### `acesso_venoso.jpg`

| Campo | Valor |
|-------|-------|
| Unidade | U3 |
| `alt` | Estudante de enfermagem administrando medicação por acesso venoso periférico |
| Legenda | Acesso venoso periférico em contexto de treinamento — usar apenas como cena de via de administração |
| Licença | Public domain |
| Autor | NATO Training Mission-Afghanistan Mass Communication Specialist 3rd Class Kirk Putnam/Mass Communication Speci |
| Original | 2136x3216 px |
| SHA-256 (12) | `3d8c874517d7` |
| Página | <https://commons.wikimedia.org/wiki/File:A_female_Afghan_student_administers_an_IV_(4509653817).jpg> |
| Uso didático | Contextualizar a via endovenosa; a cena não é protocolo brasileiro |

## 2. Reuso de outras matérias

| Ativo | Origem | Uso aqui |
|-------|--------|----------|
| `descarte_perfurocortante.jpg` | 03 Biossegurança | U11 — descarte |
| `higienizacao_maos_*.jpg` | 03 Biossegurança | U11 — assepsia |
| `epi_*.jpg` | 03 Biossegurança | U11 — preparo seguro |
| `fig_cinco_momentos.svg` (própria) | 03 | U4 — momento de higiene antes do preparo |

O reuso evita duplicar ativo e mantém a mesma legenda em todo o curso.

## 3. Lacunas de imagem

| Lacuna | Por que | Consequência |
|--------|---------|--------------|
| Sítios de injeção ID, SC e IM | Não há figura livre com qualidade anatômica confiável | **Figura própria em SVG** na produção (prevista) |
| Escala de gotejamento em equipo | Difícil de fotografar com legibilidade | Diagrama próprio em SVG |
| Rótulo de medicamento de alta vigilância | Exigiria dado institucional (F8) | Aguarda Q1 |
| Ampolas e frascos identificáveis | Nome comercial e dose visíveis | **Deliberadamente ausente** |

## 4. Regras de uso

1. `alt` sempre preenchido; legenda com licença e autor.
2. Nunca recortar de forma que esconda o contexto de dispositivo.
3. Figura não substitui texto: a fórmula e a unidade vão em texto, nunca em imagem.
4. `srcset` com variantes de 480/800/1200 px (RM-03) quando entrar no mock.

## 5. Conferência

- [x] 4 ativos baixados e conferidos.
- [x] Licença, autor e página de origem declarados.
- [x] `alt` e legenda em português.
- [x] Hash registrado.
- [ ] Variantes 480/800 px para uso no mock.
- [ ] Figuras próprias de sítios de injeção e gotejamento.
