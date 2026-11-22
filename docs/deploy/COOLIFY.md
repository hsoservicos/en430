# 🚀 Deploy no Coolify — Sistema de Avaliação EN_430

Documento de referência do deploy **oficial** do projeto. Reflete a configuração
real da instância Coolify (`servitec-local`, v4.3.14) capturada em novembro de 2026.

---

## 1. Visão geral

| Item | Valor |
|------|-------|
| **Projeto Coolify** | `en430` (`gnz9vxn3bpapljo02umrjizl`) |
| **Aplicação** | `en430.git:master-xbeqyuqmnwntusohux0n6du8` (`2iweyqxekxmminhiwgyv5bvk`) |
| **Repositório** | `hsoservicos/en430.git` — branch `master` |
| **Build pack** | `dockerfile` |
| **Base directory** | `/` (raiz do repositório) |
| **Porta exposta** | `80` (Apache) |
| **Domínio** | `https://en430.appservice.tec.br` (HTTPS forçado) |
| **Materiais** | `https://en430.appservice.tec.br/materiais/html/index_estudos.html` |
| **Health check** | `GET /` · HTTP 200 · intervalo 5s |
| **Auto-deploy** | Habilitado (push na `master`) |
| **Status** | `running:healthy` |
| **Última publicação** | 2026-11-21 (branch `master`) |
| **Materiais de estudo** | `/materiais/html/index_estudos.html` (HTML) e `/materiais/pdf/` |

> `aprendizado_em_ia` **não é publicado**. O projeto e a aplicação que existiam no
> Coolify foram removidos; o diretório permanece apenas como base de apoio local.

---

## 2. Pré-requisitos

- Repositório GitHub acessível ao Coolify (GitHub App ou deploy key).
- Instância Coolify com Docker e Traefik ativos.
- CLI Coolify autenticada (`coolify context list`).

---

## 3. Configuração da aplicação

### 3.1 Build

| Configuração | Valor |
|--------------|-------|
| Build pack | `dockerfile` |
| Dockerfile | `/Dockerfile` (contexto = raiz do repositório) |
| Base directory | `/` |
| Porta | `80` |
| Imagem base | `php:8.6-apache` (suporte de segurança até 31/12/2030) |

O Dockerfile já habilita `rewrite`, `headers`, `expires` e `deflate`, configura
`AllowOverride All` para o `.htaccess`, copia a aplicação e os materiais (`/materiais/`)
e define `APP_DB_PATH=/data/avaliacao.db`.

### 3.2 Variáveis de ambiente

| Variável | Obrigatória | Exemplo / Observação |
|----------|:-----------:|----------------------|
| `APP_DB_PATH` | ✅ | `/data/avaliacao.db` — deve coincidir com o volume |
| `APP_DEBUG` | — | `0` em produção |
| `SECRET_KEY` | ✅ | string longa e aleatória (sessão) |
| `ADMIN_SECRET` | ✅ | senha do painel `/admin` — troque o padrão |

Segredos ficam **somente** no Coolify (Environment Variables) e nunca no repositório.
O `.env.example` do projeto lista os nomes sem valores.

### 3.3 Volume persistente

| Volume | Mount path | Finalidade |
|--------|-----------|------------|
| `2iweyqxekxmminhiwgyv5bvk-en430-data` | `/data` | Banco SQLite (`avaliacao.db` + WAL) e persistência entre deploys |

Sem esse volume, o banco é recriado a cada deploy. O `entrypoint.sh` executa
`scripts/migrate.php` automaticamente quando o banco está ausente ou vazio.

### 3.4 Health check

- Caminho: `/`
- Método: `GET`
- Código esperado: `200`
- Intervalo: `5s` · timeout `5s` · retries `10` · start period `5s`

O Dockerfile também declara um `HEALTHCHECK` próprio, que requisita `index.php`.

---

### 3.5. Navegação de estudo dentro do app

| Origem | Ação |
|--------|------|
| Home `/` | Botão **📚 Materiais de Estudo** → `/materiais/html/index_estudos.html` |
| Login `/login` | Botão **← Voltar ao início** (`/`) e link **📚 Materiais de estudo** |
| Central de Estudos | Botão **📝 Ir para o Sistema de Avaliação** (`/`) |

Os PDFs são servidos diretamente do diretório copiado no build; não dependem do banco.

---

### 3.6 Varredura de segurança da imagem (Trivy)

O workflow [`.github/workflows/trivy.yml`](../../.github/workflows/trivy.yml) constrói a imagem a
partir do `Dockerfile` e a escaneia com Trivy a cada push/PR na `master` e semanalmente
(segunda-feira, 06:00 UTC). O pipeline falha quando há vulnerabilidade **HIGH** ou **CRITICAL**
com correção disponível; as sem correção continuam no relatório, mas não bloqueiam.

Para reproduzir localmente:

```bash
docker build -t en430:local .
docker run --rm -v /var/run/docker.sock:/var/run/docker.sock \
  aquasecurity/trivy:latest image --severity HIGH,CRITICAL --ignore-unfixed en430:local
```

O Coolify continua publicando a imagem pelo próprio build; a varredura é o portão de qualidade
do repositório. Se o workflow falhar, trate os achados (ou atualize a base) antes de considerar o
deploy concluído.

---

## 4. Primeiro deploy

### 4.1 Pela interface

1. **Projects → en430 → Production → + New Resource → Public Repository**.
2. Informe `https://github.com/hsoservicos/en430.git` e a branch `master`.
3. Build pack **Dockerfile**; *Base Directory* `/` (a raiz do repositório).
4. Porta `80`; domínio `en430.appservice.tec.br` com HTTPS.
5. Adicione as variáveis da seção 3.2.
6. Crie o volume persistente em `/data`.
7. **Deploy**.

### 4.2 Pela CLI

```bash
coolify context use servitec-local

coolify app create public \
  --server-uuid <SERVER_UUID> \
  --project-uuid gnz9vxn3bpapljo02umrjizl \
  --environment-name production \
  --git-repository https://github.com/hsoservicos/en430.git \
  --git-branch master \
  --build-pack dockerfile \
  --base-directory / \
  --ports-exposes 80
```

Depois configure domínio, variáveis, volume e health check pela interface
(ou pelos comandos `coolify app env create` e `coolify app storage create`).

### 4.3 Verificação

```bash
coolify app get 2iweyqxekxmminhiwgyv5bvk
curl -sI https://en430.appservice.tec.br/ | head -1      # HTTP/2 200
curl -sI https://en430.appservice.tec.br/materiais/html/index_estudos.html | head -1
curl -s  https://en430.appservice.tec.br/login | head -5 # HTML do login
```

---

## 5. Operação

| Ação | Comando |
|------|---------|
| Ver status | `coolify app get 2iweyqxekxmminhiwgyv5bvk` |
| Ver logs | `coolify app logs 2iweyqxekxmminhiwgyv5bvk --lines 100` |
| Listar variáveis | `coolify app env list 2iweyqxekxmminhiwgyv5bvk` |
| Listar volumes | `coolify app storage list 2iweyqxekxmminhiwgyv5bvk` |
| Forçar deploy | `coolify deploy uuid 2iweyqxekxmminhiwgyv5bvk` |

### Backup do banco

```bash
docker exec <container> php -r \
  '$d=new PDO("sqlite:/data/avaliacao.db"); echo $d->query("PRAGMA integrity_check")->fetchColumn();'

docker cp <container>:/data/avaliacao.db backup_$(date +%Y%m%d).db
```

O volume `/data` é a fonte de verdade; o host path fica sob
`/var/lib/docker/volumes/2iweyqxekxmminhiwgyv5bvk-en430-data/_data`.

### Rotina de contas inativas (agendamento)

A retenção de contas inativas (Política de Privacidade, item 8.2) roda por linha de comando e deve
ser agendada uma vez por dia — no **Coolify → Scheduled Tasks** ou no cron do host:

```bash
# Coolify: container = en430, frequência diária
docker exec <container> php /var/www/html/scripts/rotina_inativas.php
```

Antes de habilitar, rode em modo de simulação e confira o resultado:

```bash
docker exec <container> php /var/www/html/scripts/rotina_inativas.php --dry-run
```

> **Entrega de e-mail:** o padrão é `MAIL_TRANSPORT=log`, que apenas registra os avisos e **não**
> exclui nenhuma conta. Para que o aviso seja realmente enviado, defina `MAIL_TRANSPORT=mail`
> (exige MTA no container) e confirme um envio de teste antes de confiar na rotina.

O painel `/admin` exibe a seção "Retenção de contas inativas" com pendentes, carência, elegíveis e a
data da última execução.

---

## 6. Procedimento de rollback

1. Identifique o commit anterior na `master`.
2. Em **Coolify → en430 → Deployments**, redeploy do commit anterior
   (ou `git revert` + push, com auto-deploy).
3. Se o schema mudou, restaure o backup do banco no volume `/data` antes de subir.
4. Valide `GET /` e o login com um usuário de teste.

---

## 7. Solução de problemas

| Sintoma | Causa provável | Ação |
|---------|----------------|------|
| `unhealthy` e 500 | Volume `/data` ausente ou sem permissão | Verifique o mount e o `chown` do entrypoint |
| Banco vazio após deploy | `APP_DB_PATH` diferente do volume | Padronize `/data/avaliacao.db` |
| 404 em rotas | `mod_rewrite`/`.htaccess` inativo | Confirme `a2enmod rewrite` e `AllowOverride All` |
| Login quebrado após deploy | `SECRET_KEY` alterada | Mantenha a mesma chave entre deploys |
| `admin` inacessível | `ADMIN_SECRET` padrão | Defina a variável e redeploy |
| Imagem não atualiza | Auto-deploy desligado | `coolify deploy uuid <uuid>` |

---

## 8. Histórico de decisões

| Data | Decisão |
|------|---------|
| 2026-11 | Deploy oficial migrado para Docker + Coolify |
| 2026-11 | Volume persistente `/data` para o SQLite |
| 2026-11 | `aprendizado_em_ia` removido do Coolify — é base de apoio, não serviço publicado |
| 2026-11 | Base directory alterada para `/` e Dockerfile movido para a raiz |
| 2026-11 | Materiais de estudo incluídos no build e servidos em `/materiais/` |
| 2026-11 | Botões de navegação estudo ↔ avaliação e retorno no login |
| 2026-11 | Varredura Trivy da imagem no GitHub Actions (push/PR + semanal) |
