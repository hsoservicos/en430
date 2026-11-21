# 📚 Documentação — Projeto EN_430

Índice central da documentação do repositório. O código de cada produto fica em
`sistema_avaliacao/` (PHP, publicado) e `aprendizado_em_ia/` (base de apoio, **não publicada**).

---

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

## 🗂️ Referências e arquivo

| Caminho | Conteúdo |
|---------|----------|
| [referencias/anotacao-curso.txt](referencias/anotacao-curso.txt) | Ementa e anotações originais da disciplina |
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

### Repositórios independentes

`aprendizado_em_ia/` é um **repositório Git independente** (`hsoservicos/aprendizado_em_ia`)
usado como base de apoio/estudo de metodologia BMAD. Ele não é versionado pelo repositório
`en430`, não é publicado no Coolify e não faz parte do runtime do sistema de avaliação.
