# 🔐 Verificação de segurança do stack — 22/11/2026

**Objeto:** versões de PHP e Apache usadas pela imagem Docker do sistema de avaliação, frente às
vulnerabilidades divulgadas pela comunidade nos últimos dias, e a configuração de segurança efetiva
do runtime.

| | |
|---|---|
| **Data da verificação** | 22 de novembro de 2026 |
| **Imagem avaliada** | `en430:teste`, construída do `Dockerfile` na `master` (`55f8c9f`) |
| **Base** | `php:8.4-apache` (Debian 13.7) |
| **Método** | Leitura dos avisos oficiais do PHP e do Apache, inspeção do container (`php -v`, `php -i`, `apache2 -M`), requisições HTTP reais e execução da suíte de testes |
| **Conclusão** | ✅ **Versões corrigidas** para os CVEs recentes · ⚠️ **configuração endurecida nesta data** · ⚠️ **PHP 8.4 sai do suporte de segurança em 31/12/2026** |

---

## 1. Versões em uso

| Componente | Versão na imagem | Situação |
|---|---|---|
| PHP | **8.4.28** (build de 19/11/2026) | Última versão da linha 8.4; é a release de segurança de 19/11 |
| Apache HTTP Server | **2.4.71** (Debian 13.7) | Última versão; corrige o RCE crítico do `mod_http2` |
| SAPI | `apache2handler` (mod_php) | Não usa PHP-FPM nem CGI — fecha duas classes de ataque recentes |
| Extensões | Core, PDO/SQLite, mbstring, openssl, sodium, OPcache, zlib… | Sem `ffi`, `imap`, `snmp`, `soap` |

---

## 2. O lote crítico de 19/11/2026 (PHP)

O PHP publicou em **19/11/2026** as versões **8.5.13, 8.4.28, 8.3.37 e 8.2.36**, corrigindo 17–18
CVEs, sendo **7 críticas (CVSS v4 9.2)**. A linha 8.4 recebe 14 dessas correções.

| CVE | Componente | Risco | Aplica-se a este projeto? |
|---|---|---|---|
| CVE-2026-129289 | PHP-FPM | Request smuggling → execução de código sem autenticação | **Não** — o projeto usa `mod_php`, não FPM |
| CVE-2026-115560 | Sessão | RCE por incompatibilidade de serializador de sessão | **Sim** (sessões file-based) — corrigido em 8.4.28 |
| CVE-2026-126714 ("phpAfterShell") | Sessão (handler de arquivo) | Corrupção de sessão que pode virar execução de código | **Sim** — corrigido em 8.4.28 |
| CVE-2026-129496 / CVE-2026-129474 | Engine / backtrace | Use-after-free, RCE em configurações específicas | Parcial — 129474 é exclusivo da 8.5 |
| CVE-2026-129292 | FPM status | XSS na página de status | **Não** — sem FPM |
| CVE-2026-109618 / CVE-2026-109619 ("PHP2Shell") | Engine | Divulgadas em 21/10 | **Sim** — corrigidas em 8.4.27; a imagem está em 8.4.28 |
| Demais (SOAP, Phar, wrapper HTTP, CGI, date) | Vários | Informação, DoS, corrupção de memória | Extensões/rotas não usadas ou já corrigidas |

**Resultado:** a imagem já roda **8.4.28**, isto é, **todas as correções de 8.4 estão presentes**.
Nenhum desses CVEs aparece no catálogo KEV da CISA e não havia relato de exploração em campo na data.

> **Atenção operacional:** o build da imagem estava quebrado desde a criação da rota
> `/politica-de-privacidade` (`.dockerignore` excluía `docs/`) e só foi corrigido no commit
> `55f8c9f`. Portanto, **a imagem em produção pode ser anterior a 19/11 e estar vulnerável**. É
> necessário que o deploy do `55f8c9f` (ou posterior) conclua para que o PHP 8.4.28 entre no ar.

---

## 3. Apache HTTP Server 2.4.71

| CVE | Módulo | Severidade | Situação |
|---|---|---|---|
| CVE-2026-119618 | `mod_http2` | **Crítica** — corrupção de heap e RCE | Corrigida em 2.4.71 (imagem está nela); **`mod_http2` não está carregado**, então o vetor está fechado por dupla razão |
| CVE-2026-106023 | `mod_md` | Importante — crash do processo filho | Corrigida em 2.4.71; `mod_md` não é usado |
| CVE-2026-109163 | `mod_vhost_alias` | Baixa — overwrite de 1 byte na pilha | Corrigida em 2.4.71; módulo não usado |

Módulos carregados no container: `php_module`, `rewrite`, `headers`, `expires`, `deflate` e o
conjunto padrão do Debian. `proxy_fcgi`, `http2` e `cgi` **não** estão ativos.

---

## 4. Achados de configuração e correções aplicadas

A inspeção mostrou que a imagem oficial `php:8.4-apache` **não carrega `php.ini`** e herda padrões
inseguros para produção. Corrigido nesta data:

| # | Achado | Risco | Correção |
|:--:|---|---|---|
| C1 | `display_errors=On` — avisos do PHP iam para o HTML do usuário (com caminho de arquivo e linha) | **Alto** — vazamento de estrutura interna e de dados de erro | `php-security.ini`: `display_errors=Off`, `log_errors=On`, `error_log=/dev/stderr` |
| C2 | `expose_php=On` — header `X-Powered-By: PHP/8.4.28` anunciava a versão exata | Médio — facilita escolha de exploit | `expose_php=Off` |
| C3 | `ServerTokens OS` + `ServerSignature On` — header `Server: Apache/2.4.71 (Debian)` | Médio — fingerprinting de versão e SO | `ServerTokens Prod`, `ServerSignature Off` (ajustado também no `security.conf`, que era carregado depois e sobrescrevia a diretiva) |
| C4 | `session.cookie_httponly=Off` e `session.use_strict_mode=Off` no runtime | Médio — roubo de cookie por XSS e fixation | `cookie_httponly=1`, `use_only_cookies=1`, `use_strict_mode=1` |
| C5 | `session.upload_progress.enabled` ligado por padrão em builds anteriores | Alto — vetor das falhas de sessão de nov/2026 | `=0` no `php-security.ini` |
| C6 | `phpunit.xml`, `composer.json`, `composer.lock` e arquivos `.sh`/`.ini` acessíveis por HTTP | Baixo — informação de infraestrutura | Ampliada a lista de bloqueio do `.htaccess` (`.xml`, `.ini`, `.sh`, `.sql`, `.bak`, `.dist`, `.yml`, `.yaml`) |
| C7 | `/scripts/*.php` respondia 200 (shebang impresso; guarda de CLI inoperante) | Médio — exposição de rotinas internas | `RewriteRule ^scripts/ - [F,L]`; shebang removido |
| C8 | Sem `open_basedir` | Baixo (defesa em profundidade) | Não aplicado: o app lê `politica.md` e `/data`; exigiria lista de caminhos e teste dedicado |

### 4.1. Evidências

```
Antes:  Server: Apache/2.4.71 (Debian)
        X-Powered-By: PHP/8.4.28
        Set-Cookie: PHPSESSID=...; path=/            (sem HttpOnly no runtime)
        GET /phpunit.xml → 200

Depois: Server: Apache
        (sem X-Powered-By)
        Set-Cookie: PHPSESSID=...; path=/; HttpOnly; SameSite=Lax
        GET /phpunit.xml · /composer.json · /politica.md · /scripts/*.php → 403
        display_errors=Off · log_errors=On · use_strict_mode=1 · upload_progress=0
        probe com E_USER_WARNING: resposta contém apenas "ok"; aviso vai para o stderr
```

Suíte de testes após o endurecimento: **180 testes, 461 asserções, 0 falhas** (PHP 8.4.28).
Smoke HTTP na imagem endurecida: `/`, `/cadastro`, `/politica-de-privacidade` (13 tabelas) e
`/admin-login` em 200; healthcheck `healthy`.

---

## 5. Riscos residuais

| ID | Risco | Severidade | Encaminhamento |
|:--:|---|:--:|---|
| R1 | **PHP 8.4 sai do suporte de segurança em 31/12/2026** (a linha 8.2 sai na mesma data) | **Alto a partir de 2027** | ✅ **Resolvido em 22/11/2026**: migração para **PHP 8.6** (seção 8). A suíte passou 100% em `php:8.6` (180 testes, 461 asserções) |
| R2 | A imagem em produção pode estar desatualizada até o deploy do `55f8c9f` concluir | Alto | ✅ **Confirmado em 22/11/2026**: produção em `PHP/8.4.28`, último deploy `627a718` (12 commits atrás) e `/politica-de-privacidade`, `/excluir-conta` e `/reconsentimento` em 404. Redeploy pendente |
| R3 | Base image `php:8.6-apache` é móvel (`:8.6-apache`), não fixada por digest | Médio | Rebuild mensal programado; opcionalmente fixar digest e atualizar de forma revisada |
| R4 | Painel administrativo usa **senha única compartilhada** (`ADMIN_SECRET`) | Alto (para dados de colaboradores) | Contas individuais com bcrypt + 2FA, já previstas na fase F4 do planejamento de colaboradores |
| R5 | Sem WAF/proxy na frente do Apache e rate limit só no login | Médio | Coolify/Traefik com limite de requisições e monitoramento de 4xx/5xx |
| R6 | Ausência de varredura contínua de imagem (Trivy/Scout) no pipeline | Médio | ✅ **Resolvido em 22/11/2026**: `.github/workflows/trivy.yml` constrói a imagem e roda Trivy (HIGH/CRITICAL) em push/PR e semanalmente |
| R7 | Nenhum transporte real de e-mail; avisos de inatividade apenas registrados | Baixo (segurança), Médio (LGPD) | Configurar MTA/provedor antes de confiar na rotina |
| R8 | HTTPS não é forçado: `http://en430...` responde 200 e não há HSTS; o cookie sai sem `Secure` porque a Cloudflare termina o TLS e o PHP recebe HTTP | Médio | Ativar *Always Use HTTPS* na Cloudflare, habilitar HSTS e considerar `X-Forwarded-Proto` no `initSession()` |

---

## 6. Rotina recomendada

1. **Acompanhar** `php.net/archive` e `httpd.apache.org/security/vulnerabilities_24.html` (ou assinar
   o boletim do PHP) semanalmente.
2. **Reconstruir a imagem** ao menos uma vez por mês e sempre que houver release de segurança —
   `docker build` já puxa a última patch da linha.
3. **Conferir no container em produção**: `php -v`, `apache2 -v` e os headers `Server`/`X-Powered-By`.
4. **Manter a linha 8.6** atualizada (a 8.4 perde o suporte em 31/12/2026) e repetir a suíte a cada
   troca de base.
5. **Escanear** a imagem (`trivy image en430:latest`) e tratar HIGH/CRITICAL.

---

## 7. Referências

- PHP 8.4.28 — anúncio oficial e changelog: <https://www.php.net/archive/2026.php> ·
  <https://www.php.net/ChangeLog-8.php>
- Avisos do lote de 19/11/2026 (17 CVEs, 7 críticas): <https://github.com/php/php-src/security/advisories>
- Análise do lote e versões corrigidas: <https://www.aikido.dev/blog/php-security-release-19-november-2026-cve-2026-115560>
- Vulnerabilidades do Apache 2.4: <https://httpd.apache.org/security/vulnerabilities_24.html>
- CVE-2026-119618 (mod_http2): <https://threatdefence.com/article/apache-http-server-cve-2026-119618-http2-rce-patching-hunting>
- Fim de suporte das linhas PHP: <https://php.watch/versions/8.4/releases/8.4.28>

---

## 8. Atualização posterior (mesmo dia) — migração para PHP 8.6

O risco R1 foi tratado na sequência da verificação. A base passou de `php:8.4-apache` para
**`php:8.6-apache`** e o runtime foi revalidado:

| Verificação | Resultado |
|---|---|
| `php -v` no container | **PHP 8.6.0** (build de 19/11/2026, ZTS) |
| Apache | **2.4.71** (inalterado) |
| `mbstring` | não vem compilado na imagem oficial 8.6; passou a ser instalado no `Dockerfile` (`libonig-dev` + `docker-php-ext-install mbstring`) |
| `composer.json` / `composer.lock` | exigem `php >=8.6`; lock regenerado (`platform` e `content-hash`) |
| Suíte completa em `php:8.6` | **180 testes, 461 asserções, 0 falhas** |
| Depreciação do PHP | `curl_close()` removido de `tests/IntegrationTest.php` (sem efeito desde a 8.0) |
| Build da imagem | `docker build` concluiu com a base 8.6 e o container executou a migração inicial do banco |

O hardening da seção 4 permanece aplicado: o `php-security.ini` é copiado para a nova base e os
módulos do Apache são reabilitados no build.

---

*Relatório de verificação · Projeto EN_430 · 22/11/2026 · escopo: versões do stack e configuração do
runtime; não substitui pentest da aplicação.*
