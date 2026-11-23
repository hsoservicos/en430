# 🔐 Auditoria — senha de acesso ao serviço Samba via terminal Windows

**Objeto:** credencial de acesso ao serviço Samba (SMB) para uso e acesso a partir do terminal
Windows, no contexto do projeto EN_430.

| | |
|---|---|
| **Data da verificação** | 23 de novembro de 2026 |
| **Escopo** | Repositório `en430` (código, Dockerfile, scripts, documentação e configuração de deploy) e arquitetura de produção documentada |
| **Método** | Varredura por `samba`, `smb`, `smbd`, `smbpasswd`, `smbclient`, `net use`; busca por arquivos `smb.conf`/`*smb*`; leitura do `Dockerfile`, `docs/deploy/`, `docs/ESTADO_ATUAL.md`, `config.php`, `.env.example` e relatórios de segurança anteriores |
| **Conclusão** | ⛔ **Não existe serviço Samba nem senha SMB configurada no projeto.** Não há credencial real a medir. O que se pode auditar são (a) a postura de credenciais existente no repositório e (b) a linha de base que uma senha Samba precisa cumprir se o serviço vier a ser implantado — ambos registrados abaixo. |

---

## 1. Escopo e evidências da busca

Foram pesquisados o repositório inteiro e os diretórios internos (`sistema_avaliacao/`,
`docs/`, `scripts/`, `materiais/`, `pesquisas/`), com as seguintes constatações:

| Verificação | Resultado |
|---|---|
| Arquivos `smb.conf`, `*smb*`, `*samba*` | **Nenhum** |
| Ocorrências de `smbd`, `smbpasswd`, `pdbedit`, `smbclient`, `net use`, `New-SmbMapping` | **Nenhuma** |
| Porta 445 / 139 declarada no `Dockerfile` | **Não** (apenas `EXPOSE 80`) |
| Pacote `samba` instalado na imagem | **Não** (`php:8.6-apache`; instala apenas `libonig-dev`/`mbstring`) |
| Usuário Samba / base de senhas (`passdb.tdb`) versionada | **Nenhum** |
| Única menção a Samba | `docs/deploy/APACHE_LINUX.md`, seção 4.1, como **opção genérica** de cópia de arquivos (`/mnt/rede/...`), sem qualquer configuração, usuário ou senha |

**Arquitetura real de acesso hoje:** aplicação PHP em container Docker publicado pelo Coolify,
atrás de Cloudflare Tunnel, com entrada apenas por **HTTP/HTTPS na porta 80**. Não há compartilhamento
de arquivos, não há SMB na borda e não há túnel encaminhando 445.

> **Consequência direta:** perguntas como “a senha do Samba é forte?”, “está reutilizada?”,
> “quando expira?” ou “está em texto plano?” **não têm resposta auditável** — não há senha de Samba
> definida neste projeto. Qualquer senha existente estaria em um host Samba externo ao repositório,
> e a auditoria dela exige acesso a esse host (seção 7).

---

## 2. Por que a senha do Samba merece tratamento próprio

Samba **não autentica pelo `/etc/shadow`**. Ele mantém a própria base de credenciais
(`tdbsam` em `/var/lib/samba/private/passdb.tdb`, por padrão), na qual a senha é guardada como
**hash NT = MD4(UTF-16LE(senha)), sem salt**. Isso muda a análise em relação a senhas bcrypt da
aplicação:

| Característica | Efeito na segurança |
|---|---|
| Hash NT **sem salt** | Duas senhas iguais geram o mesmo hash; permite tabelas pré-computadas e comparação entre contas |
| MD4 é **rápido** | Uma base `passdb.tdb` vazada é quebrada por força bruta muito mais rápido que bcrypt |
| Comprimento > complexidade | Como não há salt, a única defesa real é **entropia**: frases longas (20+ caracteres) valem mais que “S3nh@!” de 8 caracteres |
| Senha Samba ≠ senha Linux | `smbpasswd` altera só a base Samba, salvo `unix password sync = yes`; as duas podem divergir |
| NTLMv2 na rede | O desafio/resposta não expõe a senha, mas permite *relay*; SMB3 com criptografia fecha esse vetor |
| Conta compartilhada | Elimina responsabilização individual — conflita com a política de acesso interno do projeto (item 6.4 da Política de Privacidade) |

---

## 3. Achados da auditoria no repositório (credenciais que existem hoje)

Como não há Samba, a auditoria encontrou achados nas **credenciais da aplicação** que afetam
qualquer acesso administrativo — inclusive o host que eventualmente rodaria um Samba.

| ID | Achado | Evidência | Severidade | Encaminhamento |
|:--:|---|---|:--:|---|
| **A1** | `ADMIN_SECRET` tem **valor padrão embutido** (`admin_enfermagem_2026`) | `sistema_avaliacao/php/config.php:19` | **Alta** | Garantir a variável no Coolify; remover o fallback do código e falhar fechado se ausente |
| **A2** | `SECRET_KEY` também tem **fallback fixo** (`enfermagem_en430_secret_key_2026`) | `config.php:18` | **Alta** | Exigir `SECRET_KEY` no ambiente; rotação invalida sessões (planejar) |
| **A3** | A senha padrão do admin está **publicada na documentação** e no `.env.example` | `docs/deploy/XAMPP_WINDOWS10.md:526,594`; `sistema_avaliacao/.env.example` | Média | Remover o valor de exemplo; usar placeholder; confirmar que produção não usa o padrão |
| **A4** | Comparação da senha admin com `===` (não `hash_equals`) e **conta única compartilhada** | `sistema_avaliacao/php/handlers.php:562,590` | Média (Alta p/ dados de colaboradores — R4 do relatório de stack) | `hash_equals()`; contas individuais + 2FA (fase F4) |
| **A5** | Senha de estudante aceita **mínimo de 4 caracteres** | `views/cadastro.php:40`; `functions.php` (`validarSenha`) | Média | Elevar para 12+ e checar senhas comuns, se houver mudança de escopo |
| **A6** | Documentação cita Samba como meio de cópia **sem orientação de segurança** | `docs/deploy/APACHE_LINUX.md:310` | Baixa | Se o caminho for usado, aplicar a linha de base da seção 5 |
| **A7** | Banco SQLite vivo em `/data` é a fonte de verdade | `docs/deploy/COOLIFY.md:3.3` | **Alta se compartilhado por SMB** | **Nunca** expor `/data/avaliacao.db` em compartilhamento de leitura/escrita; SMB + WAL pode corromper o banco. Compartilhar apenas **cópias de backup** |

> **Não há achado A0 de Samba** porque não há configuração, usuário ou senha SMB no escopo.
> A ausência de achados não significa conformidade: significa **inexistência do serviço**.

---

## 4. Linha de base de senha para acesso SMB pelo Windows

Se o Samba for realmente necessário, a senha e a configuração devem atender a este mínimo:

### 4.1. Política de senha

- **20+ caracteres** gerados aleatoriamente ou frase-senha de 4+ palavras; nunca 8–12 caracteres “complexos”.
- **Exclusiva** para o Samba — não reutilizar senha de SSH, Coolify, e-mail ou admin da aplicação.
- Gerada e guardada em **gerenciador de senhas**; nunca em documento, issue, chat, script, `smb.conf` ou Git.
- **Sem troca periódica obrigatória**; rotacionar em suspeita de vazamento, saída de pessoa ou troca de função.
- Uma conta por pessoa (ou uma conta de serviço restrita e documentada), com desativação imediata no desligamento.

Geração e cadastro (no servidor Samba):

```bash
# gerar 24 bytes aleatórios (aprox. 32 caracteres)
openssl rand -base64 24

# usuário dedicado, sem shell de login
sudo useradd --system --no-create-home --shell /usr/sbin/nologin smb_en430
sudo smbpasswd -a smb_en430          # digite a senha gerada (não passe por argumento)
sudo smbpasswd -e smb_en430          # habilitar
sudo pdbedit -L -v                   # conferir usuário e validade (sem exibir hash)
```

### 4.2. `smb.conf` endurecido (exemplo para LAN/VPN)

```ini
[global]
   workgroup = EN430
   server string = EN430 backup
   server min protocol = SMB3_00
   client min protocol = SMB3_00
   server smb encrypt = required
   ntlm auth = ntlmv2-only
   lanman auth = no
   map to guest = never
   restrict anonymous = 2
   disable netbios = yes
   smb ports = 445
   hosts allow = 10.10.0.0/24 192.168.56.0/24
   hosts deny = 0.0.0.0/0
   log file = /var/log/samba/log.%m
   log level = 1 auth:3
   max log size = 1000

[backups]
   path = /srv/en430-backups
   comment = Copias do banco (somente leitura)
   browseable = yes
   read only = yes
   guest ok = no
   valid users = smb_en430
   force user = smb_en430
   create mask = 0640
   directory mask = 0750
   follow symlinks = no
   wide links = no
   # auditoria opcional de acessos
   vfs objects = full_audit
   full_audit:prefix = %u|%I|%m|%S
   full_audit:success = connect open read
   full_audit:failure = connect
   full_audit:facility = LOCAL7
   full_audit:priority = NOTICE
```

Validar antes de reiniciar:

```bash
sudo testparm -s
sudo systemctl restart smbd
sudo smbstatus -b          # sessões abertas
sudo smbstatus --locks     # arquivos travados
```

> **Escopo do compartilhamento:** apenas arquivos de backup/transferência. O banco vivo
> (`/data/avaliacao.db`) e o diretório da aplicação **não** devem ser expostos por SMB.

### 4.3. Acesso pelo terminal Windows

No **Prompt de Comando** (o `*` solicita a senha sem gravá-la no histórico):

```bat
net use \\servidor\backups /user:servidor\smb_en430 *
dir \\servidor\backups
copy \\servidor\backups\avaliacao-20261123.db .
net use \\servidor\backups /delete
```

No **PowerShell** (credencial solicitada em janela segura):

```powershell
$cred = Get-Credential -UserName 'servidor\smb_en430'
New-SmbMapping -RemotePath '\\servidor\backups' -Credential $cred
Get-ChildItem '\\servidor\backups'
Remove-SmbMapping -RemotePath '\\servidor\backups'
```

Cuidados no Windows:

- **Nunca** digitar a senha inline (`net use ... senha`) — ela fica no histórico do PowerShell
  (`ConsoleHost_history.txt`), no log de comandos e em capturas de tela.
- `cmdkey /list` mostra credenciais salvas; remova as que não usa (`cmdkey /delete:servidor`).
- `net use * /delete /y` encerra todas as sessões SMB abertas.
- Não mapear unidade permanente em máquina compartilhada; mapear sob demanda.
- Se a conexão for negada, conferir no servidor `smbstatus`, `/var/log/samba/log.smbd` e a
  diretiva `hosts allow` — não “resolver” liberando convidado.

### 4.4. Rede

- **Não expor 445/139 na internet** nem atrás do Cloudflare Tunnel. SMB é alvo recorrente de
  ransomware e *relay* NTLM; a Cloudflare não oferece proxy SMB suportado.
- Acesso somente por **LAN confiável ou VPN** (WireGuard/Tailscale), com firewall liberando
  445 apenas para a faixa de gestão.
- Desativar NetBIOS (137–139) e SMB1 em estações e servidor.
- Se o objetivo for apenas transferir arquivos para o servidor, **preferir SSH/SFTP/SCP**
  (OpenSSH nativo no Windows 10/11) ou o backup do Coolify — sem serviço SMB adicional.

---

## 5. Checklist para auditar a senha quando o serviço existir

| # | Item | Como verificar | Critério aceitável | Severidade se falhar |
|:--:|---|---|---|:--:|
| 1 | Convidado/anônimo desabilitado | `testparm -s \| grep -Ei 'guest\|anonymous'` | `map to guest = never`, `guest ok = no`, `restrict anonymous = 2` | **Crítica** |
| 2 | Protocolo mínimo SMB3 | `testparm -s \| grep -i 'min protocol'` | `SMB3_00` ou superior; SMB1 ausente | **Crítica** |
| 3 | Criptografia SMB | `testparm -s \| grep -i 'smb encrypt'` | `required` (ou ao menos *signing* obrigatório) | Alta |
| 4 | NTLMv2 apenas | `testparm -s \| grep -Ei 'ntlm\|lanman'` | `ntlm auth = ntlmv2-only`, `lanman auth = no` | Alta |
| 5 | Sem senha nula | `testparm -s \| grep -i 'null passwords'` | `no` | **Crítica** |
| 6 | Contas e validade | `pdbedit -L -v` | Só contas necessárias, habilitadas, uma por pessoa/serviço | Alta |
| 7 | Força/unicidade da senha | Comparar com o gerenciador de senhas; nunca pedir a senha em claro | 20+ caracteres, exclusiva, sem reuso | Alta |
| 8 | Hash protegido | `ls -l /var/lib/samba/private/passdb.tdb` | `root:root`, modo `0600`; sem cópia em backup aberto | **Crítica** |
| 9 | Compartilhamento restrito | `testparm -s` (seções das shares) | `valid users`, `read only`, `hosts allow`, sem `guest ok` | Alta |
| 10 | Senha fora de scripts/docs | `rg -i 'net use\|smbpasswd\|password'` no repositório e no host | Nenhuma senha em texto plano | Alta |
| 11 | Sessões e acessos | `smbstatus -b`, `log.smbd`, Eventos Windows 4624/4625/5140/5145 | Sessões conhecidas; falhas monitoradas | Média |
| 12 | Força bruta | `fail2ban-client status samba` (se usado) ou análise de `log.smbd` | Bloqueio após tentativas repetidas; alerta em picos de 4625 | Média |
| 13 | Escopo de dados | Conteúdo da share | Sem banco vivo, sem dados pessoais além do necessário (LGPD) | **Crítica** |

Evidências a coletar no host (sem expor a senha): `testparm -s`, `pdbedit -L -v`,
`smbstatus -b`, `ss -ltnp | grep -E ':445|:139'`, regras de firewall e trecho de
`/var/log/samba/log.smbd` com falhas de autenticação.

---

## 6. Riscos e recomendação

| ID | Risco | Severidade | Recomendação |
|:--:|---|:--:|---|
| S1 | Criar Samba só para “acessar por Windows” amplia a superfície de ataque sem necessidade | Alta | Usar SSH/SFTP/SCP ou backup do Coolify; SMB apenas se houver requisito real de compartilhamento |
| S2 | Exposição de 445 na internet/túnel | **Crítica** | Não expor; LAN/VPN + firewall + SMB3 obrigatório |
| S3 | Senha fraca/reutilizada em hash NT sem salt | Alta | 20+ caracteres exclusivos, gerenciador de senhas, rotação por evento |
| S4 | Compartilhar `/data` ou a raiz da aplicação | **Crítica** | Somente cópias de backup em diretório separado e `read only` |
| S5 | Conta SMB compartilhada sem rastreabilidade | Média | Conta individual; log de auditoria; desativação no desligamento (Política 6.4) |
| S6 | Credencial em histórico do Windows/script | Alta | `*`/`Get-Credential`; nunca inline; limpar mapeamentos e histórico |
| S7 | `ADMIN_SECRET`/`SECRET_KEY` padrão em eventual host novo | Alta | Definir segredos fortes no ambiente e remover fallbacks (A1/A2) |

---

## 7. Conclusão

1. **Não existe senha de Samba a auditar neste projeto.** O serviço não está instalado,
   configurado, documentado operacionalmente nem exposto; a única menção é uma alternativa
   genérica de cópia de arquivos.
2. **A senha não é o único controle:** mesmo uma senha forte é insuficiente com convidado
   habilitado, SMB1, NTLMv1, share gravável ou 445 exposto. A linha de base combina política de
   senha, protocolo/criptografia, restrição de share e rede.
3. **O acesso administrativo atual é HTTP/HTTPS via Cloudflare**, sem SMB. Para transferência de
   arquivos pelo Windows, SSH/SFTP resolve com menos risco.
4. **Achados reais do repositório** (seção 3) seguem válidos e independentes do Samba: os mais
   graves são os fallbacks de `ADMIN_SECRET`/`SECRET_KEY` e o risco de compartilhar o SQLite vivo.
5. **Para auditar uma senha Samba real**, é necessário o host onde o serviço roda e as evidências
   da seção 5; sem isso, qualquer parecer sobre força, idade ou reuso da senha seria especulação.

---

## 8. Referências

- Samba Team — `smb.conf` (parâmetros `server min protocol`, `server smb encrypt`,
  `ntlm auth`, `restrict anonymous`, `pdbedit`): <https://www.samba.org/samba/docs/current/man-html/smb.conf.5.html>
- Samba Team — `smbpasswd`: <https://www.samba.org/samba/docs/current/man-html/smbpasswd.8.html>
- Samba Team — `pdbedit`: <https://www.samba.org/samba/docs/current/man-html/pdbedit.8.html>
- Microsoft — `net use`: <https://learn.microsoft.com/windows-server/administration/windows-commands/net-use>
- Microsoft — `New-SmbMapping`: <https://learn.microsoft.com/powershell/module/smbshare/new-smbmapping>
- Projeto EN_430 — relatório de segurança do stack (R4, conta admin única):
  [`RELATORIO_SEGURANCA_STACK_2026-11-22.md`](RELATORIO_SEGURANCA_STACK_2026-11-22.md)
- Projeto EN_430 — Política de Privacidade, item 6.4 (credencial individual):
  [`../juridico/POLITICA_DE_PRIVACIDADE.md`](../juridico/POLITICA_DE_PRIVACIDADE.md)
