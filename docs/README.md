# 📚 Documentação — Projeto EN_430

Índice central da documentação do repositório. O código de cada produto fica em
`sistema_avaliacao/` (PHP, publicado) e `aprendizado_em_ia/` (base de apoio, **não publicada**).

---

## 📍 Estado atual

| Documento | Conteúdo |
|-----------|----------|
| [**ESTADO_ATUAL.md**](ESTADO_ATUAL.md) | **Fonte de verdade:** deploy, estrutura, números, navegação e verificações |
| [**GOVERNANCA_PROGRESSO.md**](GOVERNANCA_PROGRESSO.md) | **Regra máxima (RM-01):** como todo progresso é medido, registrado e auditado — prevalece sobre os demais documentos |
| [**RM-02_PRECISAO_DIDATICA_E_VERACIDADE.md**](RM-02_PRECISAO_DIDATICA_E_VERACIDADE.md) | **Regra de base imutável (RM-02):** precisão e didática obrigatórias; nenhum material é elaborado sem verificação de veracidade registrada |
| [**RM-03_RESPONSIVIDADE.md**](RM-03_RESPONSIVIDADE.md) | **Regra de base imutável (RM-03):** responsividade como máxima garantia operacional — layout, texto e imagem se ajustam ao tamanho da tela |
| [**RM-04_ILUSTRACAO_E_RECURSOS_VISUAIS.md**](RM-04_ILUSTRACAO_E_RECURSOS_VISUAIS.md) | **Regra de base imutável (RM-04):** ilustração obrigatória — toda matéria é elaborada com banco de imagens licenciadas, com legenda, `alt` e crédito |

## 🎓 Planejamento e currículo

| Documento | Conteúdo |
|-----------|----------|
| [**ementa.md**](ementa.md) | **Ementa de referência do curso:** 1.200h + 600h de estágio, 4 módulos e 17 componentes |
| [**PROGRESSO_PLANEJAMENTO.md**](planejamento/PROGRESSO_PLANEJAMENTO.md) | **Fonte de verdade do planejamento:** gates, questões abertas, plano de ação e checklist do G0-C |
| [**INFRAESTRUTURA_NOVO_PROJETO.md**](planejamento/INFRAESTRUTURA_NOVO_PROJETO.md) | **Infraestrutura isolada do novo projeto:** ADR-001, arquitetura-alvo, provisionamento, backup e critérios do G0-C |
| [**PRD_APOIO_FORMACAO_TECNICA_ENFERMAGEM.md**](planejamento/PRD_APOIO_FORMACAO_TECNICA_ENFERMAGEM.md) | **PRD:** visão de produto, limites legais, personas, requisitos, roadmap e critérios de aceite |
| [**EXPANSAO_CURSO_TECNICO_ENFERMAGEM.md**](planejamento/EXPANSAO_CURSO_TECNICO_ENFERMAGEM.md) | **Plano de expansão:** diagnóstico, arquitetura-alvo, matriz de cobertura, roadmap e riscos |
| [**PLANEJAMENTO_FORMULARIO_COLABORADORES.md**](planejamento/PLANEJAMENTO_FORMULARIO_COLABORADORES.md) | **Formulário de colaboradores:** campos, fluxo de triagem, geração do termo, modelo de dados e painel de conformidade |

## ⚖️ Jurídico e conformidade

| Documento | Conteúdo |
|-----------|----------|
| [**juridico/README.md**](juridico/README.md) | **Índice jurídico** e registro de emissão dos termos por colaborador |
| [**TERMO_CONFIDENCIALIDADE_SIGILO.md**](juridico/TERMO_CONFIDENCIALIDADE_SIGILO.md) | **Termo de Confidencialidade e Sigilo (v1.0):** modelo preenchível — sigilo, LGPD, cessão de direitos e sanções |
| [**TERMO_ADESAO_PROFESSOR_VALIDADOR.md**](juridico/TERMO_ADESAO_PROFESSOR_VALIDADOR.md) | **Termo de Adesão do Professor Validador (v1.0):** revisão técnica e pedagógica, parecer e responsabilidade |
| [**POLITICA_DE_PRIVACIDADE.md**](juridico/POLITICA_DE_PRIVACIDADE.md) | **Política de Privacidade (v1.0):** dados dos estudantes, bases legais, retenção e direitos (LGPD) |
| [**AVISO_DE_TRATAMENTO_DE_DADOS.md**](juridico/AVISO_DE_TRATAMENTO_DE_DADOS.md) | **Aviso de Tratamento de Dados (v1.0):** resumo para a tela de cadastro e textos prontos |

> Obrigatório para todo colaborador(a) que acesse conteúdo não publicado, código, dados de
> estudantes ou informações comerciais do projeto. O exemplar assinado não é versionado neste
> repositório público.

## 🚀 Deploy

| Documento | Conteúdo |
|-----------|----------|
| [**COOLIFY.md**](deploy/COOLIFY.md) | **Deploy oficial atual** — Docker + Coolify, variáveis, volume, health check |
| [CHECKLIST.md](deploy/CHECKLIST.md) | Checklist de verificação pós-deploy |
| [APACHE_LINUX.md](deploy/APACHE_LINUX.md) | Legado — publicação manual em Apache/Linux |
| [APACHE_WINDOWS.md](deploy/APACHE_WINDOWS.md) | Legado — publicação manual em Apache/Windows |
| [XAMPP_WINDOWS10.md](deploy/XAMPP_WINDOWS10.md) | Legado — ambiente XAMPP no Windows 10 |

> Os guias de Apache/XAMPP continuam válidos para instalações locais sem container,
> mas o ambiente oficial de produção é o **Coolify** (`en430.appservice.tec.br`).

## 🔎 Auditorias e relatórios

| Documento | Conteúdo |
|-----------|----------|
| [AUDITORIA_PROJETO_ENFERMAGEM.md](auditorias/AUDITORIA_PROJETO_ENFERMAGEM.md) | Auditoria técnica: arquitetura, segurança, testes, banco |
| [RELATORIO_EVOLUCAO.md](auditorias/RELATORIO_EVOLUCAO.md) | Evolução do projeto, métricas e cobertura |
| [RELATORIO_SEGURANCA_STACK_2026-11-22.md](auditorias/RELATORIO_SEGURANCA_STACK_2026-11-22.md) | **Segurança do stack:** CVEs de PHP/Apache de nov/2026, hardening do runtime e riscos residuais |
| [AUDITORIA_ARTEFATOS_PESQUISA_2026-11-23.md](auditorias/AUDITORIA_ARTEFATOS_PESQUISA_2026-11-23.md) | **Artefatos de pesquisa:** 21 fichas verificadas, 7 achados e 1 correção |

## 🗂️ Referências e arquivo

| Caminho | Conteúdo |
|---------|----------|
| [ementa.md](ementa.md) | Matriz curricular completa do curso Técnico em Enfermagem |
| [referencias/anotacao-curso.txt](referencias/anotacao-curso.txt) | Anotações originais da disciplina EN_430 |
| [arquivo/artefatos_python.7z](arquivo/artefatos_python.7z) | Versão original em Python/Flask (histórico) |
| [arquivo/imagem-whatsapp-2026-07-15.jpeg](arquivo/imagem-whatsapp-2026-07-15.jpeg) | Imagem de referência do material |

---

## 📐 Padrões do repositório

| Área | Padrão |
|------|--------|
| **Documentação** | Markdown em `docs/`; README na raiz apenas como visão geral e índice |
| **Materiais didáticos** | HTML em `materiais/html/`, PDFs em `materiais/pdf/`; links relativos entre eles |
| **Scripts operacionais** | `scripts/` na raiz (instalação, verificação) |
| **Código de produto** | Um diretório por produto (`sistema_avaliacao/`, `aprendizado_em_ia/`), autocontido |
| **Deploy** | Dockerfile por produto; produção via Coolify; segredos apenas em variáveis de ambiente |
| **Versionamento** | `master` com commits convencionais (`feat:`, `fix:`, `docs:`, `chore:`) |
| **Qualidade** | PHPUnit em `sistema_avaliacao/php/tests`; verificação de links com `scripts/verificar_links.py` |
| **Progresso** | Toda declaração de avanço segue a [RM-01](GOVERNANCA_PROGRESSO.md): evidência, fonte de verdade por domínio, registro no mesmo commit e histórico imutável |
| **Conteúdo didático** | Todo material segue a [RM-02](RM-02_PRECISAO_DIDATICA_E_VERACIDADE.md): fonte primária conferida e datada, didática verificável e revisão humana antes de publicar |
| **Interface** | Toda tela segue a [RM-03](RM-03_RESPONSIVIDADE.md): responsiva nas 8 larguras de teste, sem rolagem horizontal e com evidência registrada |
| **Ilustração** | Toda matéria segue a [RM-04](RM-04_ILUSTRACAO_E_RECURSOS_VISUAIS.md): banco de imagens licenciadas, legenda didática e `alt`, sem hotlink e sem figura sem licença |

### Repositórios independentes

`aprendizado_em_ia/` é um **repositório Git independente** (`hsoservicos/aprendizado_em_ia`)
usado como base de apoio/estudo de metodologia BMAD. Ele não é versionado pelo repositório
`en430`, não é publicado no Coolify e não faz parte do runtime do sistema de avaliação.
