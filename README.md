# 🎓 EN_430 — Introdução à Enfermagem

<div align="center">

![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=flat&logo=php&logoColor=white)
![SQLite](https://img.shields.io/badge/SQLite-3.x-003B57?style=flat&logo=sqlite&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-ready-2496ED?style=flat&logo=docker&logoColor=white)
![Coolify](https://img.shields.io/badge/Deploy-Coolify-6C47FF?style=flat)
![PHPUnit](https://img.shields.io/badge/PHPUnit-11.x-007EC6?style=flat)
![License](https://img.shields.io/badge/License-Educational-blue?style=flat)

</div>

Repositório da disciplina **Introdução à Enfermagem (EN_430)** — curso Técnico em
Enfermagem (EAD/Subsequente). Reúne o **sistema de avaliação online**, os
**materiais didáticos** e a **documentação operacional**.

- 🌐 **Sistema em produção:** <https://en430.appservice.tec.br>
- 📚 **Materiais:** [`materiais/`](materiais/)
- 🚀 **Deploy:** [Docker + Coolify](docs/deploy/COOLIFY.md)

---

## 📂 Estrutura do repositório

```text
en430/
├── index.html                    # Portal de entrada dos materiais
├── README.md                     # Este documento
├── CHANGELOG.md                  # Histórico de versões
├── .editorconfig / .gitattributes
│
├── docs/                         # 📚 Documentação
│   ├── ESTADO_ATUAL.md           #   Fonte de verdade do projeto
│   ├── ementa.md                 #   Matriz curricular do curso Técnico em Enfermagem
│   ├── planejamento/             #   Plano de expansão para todo o curso
│   ├── README.md                 #   Índice central + padrões do repositório
│   ├── deploy/
│   │   ├── COOLIFY.md            #   Deploy oficial (Docker + Coolify)
│   │   ├── CHECKLIST.md          #   Verificação pós-deploy
│   │   ├── APACHE_LINUX.md       #   Legado: Apache/Linux
│   │   ├── APACHE_WINDOWS.md     #   Legado: Apache/Windows
│   │   └── XAMPP_WINDOWS10.md    #   Legado: XAMPP
│   ├── auditorias/               #   Auditoria técnica e relatório de evolução
│   ├── referencias/              #   Ementa e anotações da disciplina
│   └── arquivo/                  #   Artefatos históricos (Python/Flask, imagem)
│
├── materiais/                    # 🎓 Material didático
│   ├── html/                     #   12 artefatos interativos
│   └── pdf/                      #   16 PDFs para impressão/download
│
├── scripts/                      # 🔧 Operação
│   ├── instalar_publicar_linux.sh
│   ├── instalar_publicar_windows.ps1
│   ├── instalar_publicar_windows.bat
│   └── verificar_links.py        #   Checagem de links relativos
│
├── sistema_avaliacao/            # 🎯 Produto publicado
│   ├── README.md
│   ├── .env.example
│   └── php/                      #   PHP 8 + Apache + SQLite (código-fonte)
│
└── aprendizado_em_ia/            # 🧪 Base de apoio (repositório independente)
```

> `aprendizado_em_ia/` é um repositório Git separado, usado como base de estudos
> e apoio ao desenvolvimento. **Não é publicado no Coolify** e não faz parte do
> runtime do sistema de avaliação.

---

## 🚀 Início rápido

### Docker (recomendado — mesma imagem da produção)

```bash
docker build -t en430 .
docker run --rm -p 8080:80 \
  -e APP_DB_PATH=/data/avaliacao.db \
  -e SECRET_KEY=dev-secret \
  -e ADMIN_SECRET=admin_dev \
  -v "$PWD/data:/data" \
  en430
# http://localhost:8080
```

### PHP embutido (desenvolvimento)

```bash
cd sistema_avaliacao/php
php scripts/migrate.php          # cria o banco SQLite
php -S 127.0.0.1:8080 -t . router.php
# http://127.0.0.1:8080
```

Requisitos locais: PHP 8.1+ com `pdo_sqlite` e `mbstring`, SQLite 3.x.
Instalação em Apache/XAMPP: guias em [`docs/deploy/`](docs/deploy/).

### Deploy em produção

O ambiente oficial roda em **Coolify** (projeto `en430`, branch `master`,
build pack Dockerfile, porta 80, volume persistente em `/data`).
Passo a passo, variáveis e rollback: **[docs/deploy/COOLIFY.md](docs/deploy/COOLIFY.md)**.

---

## 🎯 Sistema de Avaliação

Plataforma web para cadastro/login de estudantes, aplicação de avaliações com
questões aleatórias por módulo e dificuldade, correção imediata, gabarito e
gráficos de progresso, além de painel administrativo.

| Item | Detalhe |
|------|---------|
| Stack | PHP 8 + Apache + SQLite (PDO) + Chart.js |
| Questões | 2.803 distribuídas em 10 módulos (Fácil/Médio/Difícil) |
| Segurança | bcrypt (cost 12), CSRF, rate limiting, session fixation, CSP e headers |
| Testes | 158 casos PHPUnit em `sistema_avaliacao/php/tests` |
| Container | `Dockerfile` na raiz (PHP 8.6 + Apache) |

```bash
cd sistema_avaliacao/php
composer install
./vendor/bin/phpunit --no-coverage
```

Detalhes técnicos: [`sistema_avaliacao/README.md`](sistema_avaliacao/README.md) ·
[`sistema_avaliacao/php/README.md`](sistema_avaliacao/php/README.md).

---

## 🎓 Materiais didáticos

Portal: [`index.html`](index.html) · Central de estudos: [`materiais/html/index_estudos.html`](materiais/html/index_estudos.html)

| Artefato | HTML | PDF |
|----------|:----:|:---:|
| Guia completo de estudos | [abrir](materiais/html/guia_completo_estudos.html) | [baixar](materiais/pdf/Guia_Completo_Estudos_Introducao_Enfermagem.pdf) |
| Plano de estudos 20h | [abrir](materiais/html/plano_de_estudos_20h.html) | [baixar](materiais/pdf/Plano_de_Estudos_20h_Introducao_Enfermagem.pdf) |
| Plano avançado EAD | [abrir](materiais/html/plano_estudos_avancado.html) | [baixar](materiais/pdf/Plano_Estudos_Avancado_EAD_Introducao_Enfermagem.pdf) |
| Resumo (5 min) | [abrir](materiais/html/resumo_uma_pagina.html) | [baixar](materiais/pdf/Resumo_Introducao_Enfermagem_5min.pdf) |
| Flashcards | [abrir](materiais/html/flashcards_enfermagem.html) | [baixar](materiais/pdf/Flashcards_Introducao_Enfermagem.pdf) |
| Mapa mental (10 módulos) | [abrir](materiais/html/mapa_mental_10_modulos.html) | [baixar](materiais/pdf/Mapa_Mental_10_Modulos_Introducao_Enfermagem.pdf) |
| Cronograma mensal | [abrir](materiais/html/cronograma_mensal.html) | [baixar](materiais/pdf/Cronograma_Mensal_Estudos_Introducao_Enfermagem.pdf) |
| Casos clínicos | [abrir](materiais/html/casos_clinicos_pratica.html) | [baixar](materiais/pdf/Casos_Clinicos_Pratica_Introducao_Enfermagem.pdf) |
| Vídeos recomendados | [abrir](materiais/html/videos_recomendados.html) | [baixar](materiais/pdf/Guia_Videos_Recomendados_Introducao_Enfermagem.pdf) |
| Simulado (40 questões) | [abrir](materiais/html/simulado_40_questoes.html) | [baixar](materiais/pdf/Simulado_40_Questoes_Introducao_Enfermagem.pdf) |
| Apostila completa | — | [baixar](materiais/pdf/Apostila_Completa_Introducao_Enfermagem.pdf) |

---

## 📚 Documentação

| Documento | Conteúdo |
|-----------|----------|
| [docs/GOVERNANCA_PROGRESSO.md](docs/GOVERNANCA_PROGRESSO.md) | **Regra máxima (RM-01):** gestão, controle, auditoria e acompanhamento de todo progresso |
| [docs/ESTADO_ATUAL.md](docs/ESTADO_ATUAL.md) | **Estado atual:** deploy, estrutura, números e verificações |
| [docs/ementa.md](docs/ementa.md) | Matriz curricular do curso Técnico em Enfermagem |
| [docs/planejamento/PRD_APOIO_FORMACAO_TECNICA_ENFERMAGEM.md](docs/planejamento/PRD_APOIO_FORMACAO_TECNICA_ENFERMAGEM.md) | **PRD do programa de apoio** à formação técnica |
| [docs/planejamento/EXPANSAO_CURSO_TECNICO_ENFERMAGEM.md](docs/planejamento/EXPANSAO_CURSO_TECNICO_ENFERMAGEM.md) | **Plano de expansão** para todo o curso |
| [docs/README.md](docs/README.md) | Índice central e padrões do repositório |
| [docs/deploy/COOLIFY.md](docs/deploy/COOLIFY.md) | Deploy oficial, variáveis, volume e rollback |
| [docs/deploy/CHECKLIST.md](docs/deploy/CHECKLIST.md) | Checklist pós-deploy |
| [docs/auditorias/AUDITORIA_PROJETO_ENFERMAGEM.md](docs/auditorias/AUDITORIA_PROJETO_ENFERMAGEM.md) | Auditoria técnica |
| [docs/auditorias/RELATORIO_EVOLUCAO.md](docs/auditorias/RELATORIO_EVOLUCAO.md) | Evolução e métricas |
| [CHANGELOG.md](CHANGELOG.md) | Histórico de versões |

---

## ✅ Qualidade e verificação

```bash
# Links relativos de HTML/Markdown
python3 scripts/verificar_links.py

# Testes PHP
cd sistema_avaliacao/php && ./vendor/bin/phpunit --no-coverage
```

---

<div align="center">

*EN_430 — Introdução à Enfermagem · PHP 8 + SQLite · Docker + Coolify · 2026*

</div>
