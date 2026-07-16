# 🪟 Guia Completo de Instalação e Publicação — Windows 10 + XAMPP

> **Projeto:** Sistema de Avaliação — Introdução à Enfermagem (EN_430)  
> **Tecnologia:** PHP 8 + SQLite + JavaScript  
> **Servidor:** XAMPP (Apache + PHP + MySQL) para Windows 10  
> **Público-alvo:** Iniciantes em administração de servidores  
> **Última atualização:** Julho 2026

---

## 📋 Sumário

1. [O que é este guia?](#1-o-que-é-este-guia)
2. [O que você precisa saber antes de começar](#2-o-que-você-precisa-saber-antes-de-começar)
3. [Visão Geral da Arquitetura](#3-visão-geral-da-arquitetura)
4. [Passo 1: Baixar o XAMPP](#4-passo-1-baixar-o-xampp)
5. [Passo 2: Instalar o XAMPP](#5-passo-2-instalar-o-xampp)
6. [Passo 3: Iniciar o Apache pela primeira vez](#6-passo-3-iniciar-o-apache-pela-primeira-vez)
7. [Passo 4: Testar o Apache](#7-passo-4-testar-o-apache)
8. [Passo 5: Obter os arquivos do projeto](#8-passo-5-obter-os-arquivos-do-projeto)
9. [Passo 6: Copiar os arquivos para o XAMPP](#9-passo-6-copiar-os-arquivos-para-o-xampp)
10. [Passo 7: Configurar o banco de dados](#10-passo-7-configurar-o-banco-de-dados)
11. [Passo 8: Testar o sistema de avaliação](#11-passo-8-testar-o-sistema-de-avaliação)
12. [Passo 9: Configurar URLs amigáveis (opcional)](#12-passo-9-configurar-urls-amigáveis-opcional)
13. [Passo 10: Configurar o painel administrativo](#13-passo-10-configurar-o-painel-administrativo)
14. [Passo 11: Liberar acesso na rede local](#14-passo-11-liberar-acesso-na-rede-local)
15. [Passo 12: Compartilhar com os estudantes](#15-passo-12-compartilhar-com-os-estudantes)
16. [Manutenção e Backup](#16-manutenção-e-backup)
17. [Solução de Problemas](#17-solução-de-problemas)
18. [Apêndice A: Comandos Rápidos](#18-apêndice-a-comandos-rápidos)
19. [Apêndice B: Checklist de Verificação](#19-apêndice-b-checklist-de-verificação)
20. [Apêndice C: Estrutura Completa de Arquivos](#20-apêndice-c-estrutura-completa-de-arquivos)

---

## 1. O que é este guia?

Este guia ensina **passo a passo** como instalar o Sistema de Avaliação da disciplina **Introdução à Enfermagem (EN_430)** em um computador com **Windows 10** usando o **XAMPP**.

### O que é o XAMPP?

XAMPP é um pacote que reúne tudo que você precisa para rodar sites PHP:
- **Apache** — Servidor web (entrega as páginas)
- **PHP** — Linguagem de programação (processa a lógica)
- **SQLite** — Banco de dados (armazena questões, estudantes, avaliações)

### Para quem é este guia?

- Professores que querem disponibilizar o sistema para os alunos
- Técnicos de TI que vão instalar o servidor
- Qualquer pessoa com conhecimentos básicos de Windows

### O que você vai ter ao final

```
📌 Um sistema web funcionando que permite:
   ✅ Estudantes se cadastrarem e fazerem login
   ✅ Realizarem avaliações com questões de enfermagem
   ✅ Verem seu progresso por módulo
   ✅ Administradores gerenciarem o sistema
   ✅ Mais de 2.800 questões de 10 módulos
```

---

## 2. O que você precisa saber antes de começar

### Conhecimentos necessários

| Você precisa saber | Explicação |
|---|---|
| ✅ O que é um arquivo `.zip` | Saber extrair arquivos compactados |
| ✅ Copiar e colar arquivos | Saber usar o Explorador de Arquivos do Windows |
| ✅ Clicar em "Avançar" em instalações | Saber usar um instalador padrão do Windows |
| ✅ Abrir o Prompt de Comando (`cmd`) | Saber digitar comandos básicos |
| ✅ Saber o IP do seu computador | Para compartilhar o sistema na rede |

### Requisitos do computador

| Requisito | Mínimo | Recomendado |
|---|---|---|
| Sistema | Windows 10 (64 bits) | Windows 10 ou 11 (64 bits) |
| Processador | Qualquer dual-core | Intel Core i3 ou superior |
| Memória RAM | 2 GB | 4 GB ou mais |
| Espaço em disco | 1 GB livres | 2 GB ou mais |
| Acesso | Administrador do computador | Administrador |

### Programas que serão instalados

| Programa | Tamanho | Onde baixar | Função |
|---|---|---|---|
| Visual C++ Redistributable 64-bit | ~30 MB | [Microsoft oficial](https://aka.ms/vs/17/release/vc_redist.x64.exe) | Necessario para Apache e PHP |
| XAMPP 8.x | ~200 MB | [Apache Friends](https://www.apachefriends.org/) | Apache + PHP + SQLite |

> **Ordem de instalacao:** Primeiro o **Visual C++** (se nao estiver instalado), depois o **XAMPP**.

---

## 3. Visão Geral da Arquitetura

Antes de começar, entenda como o sistema vai funcionar:

```
┌─────────────────────────────────────────────────────────────────────┐
│                      SEU COMPUTADOR WINDOWS 10                      │
│                                                                     │
│  ┌─────────────────────────────────────────────────────────────┐    │
│  │                    XAMPP Control Panel                       │    │
│  │  ┌──────────┐  ┌──────────┐                                │    │
│  │  │ Apache   │  │   PHP    │  (ambos rodando como serviços)  │    │
│  │  │ Servidor │  │ Processa │                                │    │
│  │  │  Web     │  │  Lógica  │                                │    │
│  │  └────┬─────┘  └────┬─────┘                                │    │
│  └───────┼──────────────┼──────────────────────────────────────┘    │
│          │              │                                          │
│          ▼              ▼                                          │
│  ┌─────────────────────────────────────────────────────────────┐    │
│  │              C:\\xampp\\htdocs\\en430\\                        │    │
│  │                                                              │    │
│  │  📄 index.html            → Página inicial                   │    │
│  │  📄 cadastro.php          → Cadastro de estudantes            │    │
│  │  📄 login.php             → Login                            │    │
│  │  📄 painel.php            → Painel do estudante              │    │
│  │  📄 avaliacao.php         → Página de avaliação              │    │
│  │  📄 resultado.php         → Resultado da avaliação           │    │
│  │  📄 progresso.php         → Progresso por módulo             │    │
│  │  📄 admin.php             → Painel administrativo            │    │
│  │  🗄️ avaliacao.db          → Banco SQLite (questões + dados)  │    │
│  └─────────────────────────────────────────────────────────────┘    │
│                                                                     │
│  Acesso dos estudantes:                                             │
│  http://192.168.X.X/en430/         → Página inicial                 │
│  http://192.168.X.X/en430/cadastro → Cadastro                      │
│  http://192.168.X.X/en430/login    → Login                         │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 4. Passo 1: Baixar o XAMPP

> ⏱️ **Tempo estimado:** 5 minutos  
> 📥 **Download:** ~200 MB

### 4.1. Abrir o navegador

1. Clique no ícone do **Microsoft Edge**, **Google Chrome** ou **Firefox** na sua área de trabalho
2. Na barra de endereços (no topo), digite: `https://www.apachefriends.org/`
3. Pressione **Enter**

### 4.2. Baixar o XAMPP

1. Na página que abrir, você verá uma seção chamada **"XAMPP para Windows"**
2. Clique no botão **"Download"** da versão mais recente (recomendado: **XAMPP 8.2.x** ou superior)
   > Escolha a versão com **PHP 8.x** (ex: `XAMPP 8.2.12` ou `XAMPP 8.3.x`)
3. O download vai começar automaticamente
4. Aguarde o download terminar (o arquivo terá cerca de **200 MB**)

> 💡 **Dica:** Se a Internet for lenta, o download pode levar alguns minutos.

---

## 5. Passo 2: Instalar o XAMPP

> ⏱️ **Tempo estimado:** 10 minutos

### 5.1. Executar o instalador

1. Vá até a pasta de **Downloads** do seu computador
2. Localize o arquivo baixado: `xampp-windows-x64-8.x.x-x-VS16-installer.exe`
3. **Clique com o botão DIREITO** sobre o arquivo
4. Selecione **"Executar como administrador"**
   > ⚠️ **Importante:** Isso evita problemas de permissão durante a instalação

### 5.2. Passos da instalação

Aparecerá uma janela do instalador. Siga estes passos:

**Tela 1 — Bem-vindo**
- Clique em **"Next >"**

**Tela 2 — Selecionar componentes**
- Mantenha as opções marcadas:
  - ✅ **Apache** (obrigatório)
  - ✅ **PHP** (obrigatório)
  - ✅ **phpMyAdmin** (opcional, mas mantenha)
  - ✅ **SQLite** (já vem incluído no PHP)
- Desmarque **MySQL** e **FileZilla** se não forem necessários (para economizar espaço)
  > 💡 O sistema **não usa** MySQL — apenas SQLite (banco em arquivo)
- Clique em **"Next >"**

**Tela 3 — Pasta de instalação**
- Mantenha o padrão: `C:\xampp`
- Clique em **"Next >"**

**Tela 4 — Idioma**
- Selecione **"English"** (inglês)
- Clique em **"Next >"**

**Tela 5 — Pronto para instalar**
- Clique em **"Next >"**

**Tela 6 — Instalação**
- Aguarde a barra de progresso completar (pode levar alguns minutos)
- 🔒 **Se aparecer um aviso do Firewall do Windows:** Clique em **"Permitir acesso"**

**Tela 7 — Concluído**
- ✅ Marque a opção **"Do you want to start the Control Panel now?"**
- Clique em **"Finish"**

---

## 6. Passo 3: Iniciar o Apache pela primeira vez

> ⏱️ **Tempo estimado:** 3 minutos

### 6.1. Abrir o XAMPP Control Panel

Após a instalação, o **XAMPP Control Panel** deve abrir automaticamente.
Se não abrir:
1. Clique no menu **Iniciar**
2. Digite **"XAMPP Control Panel"**
3. Clique no ícone que aparecer

### 6.2. Iniciar o Apache

Você verá uma janela com uma lista de serviços:

```
XAMPP Control Panel v3.x.x
┌────────────┬──────────┬─────────┬──────────┐
│ Service    │ PID(s)   │ Port(s) │ Actions  │
├────────────┼──────────┼─────────┼──────────┤
│ Apache     │          │         │ [Start]  │
│ MySQL      │          │         │ [Start]  │
│ FileZilla  │          │         │ [Start]  │
└────────────┴──────────┴─────────┴──────────┘
```

1. Clique no botão **"Start"** ao lado de **Apache**
2. Aguarde alguns segundos
3. Você verá `PID: xxxx` e `Port(s): 80` aparecerem ao lado do Apache
   - ✅ **Verde:** Apache está rodando!
   - ❌ **Vermelho:** Algo deu errado (veja a seção de [Solução de Problemas](#17-solução-de-problemas))

### 6.3. Configurar inicialização automática (opcional)

Para que o Apache inicie automaticamente quando o computador ligar:

1. No XAMPP Control Panel, clique no botão **"Config"** ao lado de **Apache**
2. Selecione **"Autostart of Module"**
3. Feche a janela de configuração

> 💡 Agora o XAMPP iniciará o Apache automaticamente sempre que você ligar o computador.

---

## 7. Passo 4: Testar o Apache

> ⏱️ **Tempo estimado:** 1 minuto

### 7.1. Abrir o navegador

1. Abra qualquer navegador (Edge, Chrome, Firefox)
2. Na barra de endereços, digite: `http://localhost`
3. Pressione **Enter**

### 7.2. Verificar resultado

Você deve ver a página **"XAMPP Dashboard"** ou **"Welcome to XAMPP"**.

> ✅ **Se você viu a página do XAMPP → O Apache está funcionando!**

> ❌ **Se não funcionou:** Veja a seção [Solução de Problemas](#17-solução-de-problemas).

---

## 8. Passo 5: Obter os arquivos do projeto

> ⏱️ **Tempo estimado:** 10 minutos

Você precisa dos arquivos do projeto. Existem 3 formas de obtê-los:

### Opção 1: Baixar do GitHub (recomendado)

> Se os arquivos estiverem em um repositório GitHub:

1. Abra o navegador
2. Acesse: `https://github.com/hsoservicos/en430`
3. Clique no botão verde **"Code"** ou **"<> Code"**
4. Clique em **"Download ZIP"**
5. Aguarde o download (cerca de **10 MB**)
6. Após baixar, extraia o arquivo ZIP para uma pasta
   - Clique com o botão direito sobre o ZIP
   - Selecione **"Extrair tudo..."**
   - Escolha uma pasta (ex: `C:\projetos\en430`)

### Opção 2: Copiar de um pendrive

> Se você recebeu os arquivos em um pendrive:

1. Conecte o pendrive ao computador
2. Abra o pendrive no Explorador de Arquivos
3. Localize a pasta `en430` ou `sistema_avaliacao`
4. Copie a pasta inteira para o seu computador (ex: `C:\projetos\en430`)

### Opção 3: Copiar de outro computador na rede

> Se os arquivos estiverem em outro computador na mesma rede:

1. No computador de origem, compartilhe a pasta pela rede
2. No seu computador, acesse o compartilhamento via `\\NOME_DO_PC\pasta`
3. Copie os arquivos para `C:\projetos\en430`

---

## 9. Passo 6: Copiar os arquivos para o XAMPP

> ⏱️ **Tempo estimado:** 5 minutos

### 9.1. Localizar a pasta do XAMPP

O XAMPP armazena os sites na pasta `htdocs`. Localize-a:

```
C:\xampp\htdocs\   ← É aqui que os sites ficam
```

### 9.2. Copiar o projeto

Agora vamos copiar os arquivos do projeto para dentro do `htdocs`:

1. Abra o **Explorador de Arquivos** do Windows (atalho: `Win + E`)
2. Navegue até `C:\xampp\htdocs\`
3. **Crie uma nova pasta** chamada `en430`
   - Clique com botão direito dentro da pasta `htdocs`
   - Selecione **"Novo" → "Pasta"**
   - Digite: `en430`
   - Pressione Enter
4. Copie TODOS os arquivos do projeto para `C:\xampp\htdocs\en430\`

**Como copiar:**

**Método 1 — Arrastar e soltar:**
1. Abra duas janelas do Explorador de Arquivos lado a lado
2. Uma com a pasta de origem (onde estão os arquivos)
3. Outra com `C:\xampp\htdocs\en430\`
4. Selecione todos os arquivos (`Ctrl + A`)
5. Arraste para a pasta de destino

**Método 2 — Copiar e colar:**
1. Abra a pasta de origem
2. Selecione todos os arquivos (`Ctrl + A`)
3. Copie (`Ctrl + C`)
4. Vá para `C:\xampp\htdocs\en430\`
5. Cole (`Ctrl + V`)

### 9.3. Copiar o sistema de avaliação PHP

Dentro da pasta `en430`, existe uma subpasta chamada `sistema_avaliacao`. Esta pasta contém o sistema PHP.

**Verifique se a estrutura ficou assim:**

```
C:\xampp\htdocs\en430\
├── index.html                        ← Página inicial do projeto
├── index_estudos.html                ← Central de Estudos
├── guia_completo_estudos.html        ← Guia de Estudos
├── ... (outros HTMLs)
├── Apostila_Completa_...pdf          ← Apostila completa
├── sistema_avaliacao\                ← Sistema de Avaliação
│   └── php\
│       ├── index.php                 ← Principal (front controller)
│       ├── config.php                ← Configurações
│       ├── functions.php             ← Funções do sistema
│       ├── handlers.php              ← Controladores
│       ├── db.php                    ← Conexão com banco
│       ├── router.php                ← Roteador (servidor embutido)
│       ├── avaliacao.db              ← Banco de dados SQLite
│       ├── assets\                   ← CSS e JavaScript
│       ├── views\                    ← Páginas do sistema
│       ├── scripts\                  ← Scripts de manutenção
│       └── tests\                    ← Testes automatizados
├── GUIA_PUBLICACAO_XAMPP_WINDOWS10.md ← Este guia
└── README.md                         ← Documentação
```

> ✅ **Verificação:** Dentro de `C:\xampp\htdocs\en430` deve existir o arquivo `index.html` e a pasta `sistema_avaliacao`.

---

## 10. Passo 7: Configurar o banco de dados

> ⏱️ **Tempo estimado:** 5 minutos

O sistema usa um banco SQLite que já está incluso nos arquivos. Mas precisamos garantir que o Apache pode escrever nele.

### 10.1. Verificar permissões do banco

1. Navegue até: `C:\xampp\htdocs\en430\sistema_avaliacao\php\`
2. Localize o arquivo: **`avaliacao.db`**
3. **Clique com o botão direito** sobre o arquivo
4. Selecione **"Propriedades"**
5. Desmarque a opção **"Somente leitura"** (se estiver marcada)
6. Clique em **"Aplicar"** e depois em **"OK"**

### 10.2. Recriar o banco de dados (opcional)

> ⚠️ **ATENÇÃO:** Só faça este passo se o banco `avaliacao.db` não existir ou estiver vazio.
> Este comando **irá apagar** todas as questões existentes e recriá-las.

Para recriar o banco com todas as 2.800 questões:

1. **Abra o Prompt de Comando como Administrador:**
   - Clique no menu Iniciar
   - Digite: `cmd`
   - **Clique com o botão direito** em "Prompt de Comando"
   - Selecione **"Executar como administrador"**
   - Clique em **"Sim"** na janela de confirmação

2. **Navegue até a pasta do sistema:**
   ```cmd
   cd C:\xampp\htdocs\en430\sistema_avaliacao\php
   ```
   > Digite este comando e pressione Enter

3. **Execute o script de recriação:**
   ```cmd
   php scripts\recriar_questoes.php
   ```

4. **Resultado esperado:** Você verá uma mensagem como:
   ```
   ========================================
     🔄 RECRIAR QUESTÕES (3000+)
   ========================================
   
      ✅ Tabela 'questoes' removida
      ✅ Tabela recriada
   
      Módulo 1: 510 questões
      Módulo 2: 250 questões
      Módulo 3: 250 questões
      Módulo 4: 258 questões
      ...
      Módulo 10: 245 questões
   
   ───────────────────────────────────────
     ✅ RECRIAÇÃO CONCLUÍDA!
   ───────────────────────────────────────
   
     📊 Total: 2803 questões
   ```

### 10.3. Testar o banco de dados

Ainda no Prompt de Comando, digite:

```cmd
php -r "$db = new PDO('sqlite:avaliacao.db'); echo 'Banco OK! Total: ' . $db->query('SELECT COUNT(*) FROM questoes')->fetchColumn() . ' questões' . PHP_EOL;"
```

**Resultado esperado:**
```
Banco OK! Total: 2803 questões
```

---

## 11. Passo 8: Testar o sistema de avaliação

> ⏱️ **Tempo estimado:** 5 minutos

### 11.1. Testar a página inicial do sistema PHP

1. Abra o navegador
2. Digite na barra de endereços:
   ```
   http://localhost/en430/sistema_avaliacao/php/
   ```
3. Pressione **Enter**

**O que você deve ver:**
✅ Página inicial do Sistema de Avaliação com:
- Título "Sistema de Avaliação — Introdução à Enfermagem"
- Botão "📝 Cadastre-se"
- Botão "🔑 Fazer Login"
- Informações sobre o sistema

### 11.2. Testar o cadastro

1. Clique em **"📝 Cadastre-se"** ou acesse:
   ```
   http://localhost/en430/sistema_avaliacao/php/cadastro
   ```
2. Preencha o formulário:
   - **Nome:** Maria Silva
   - **Data de nascimento:** 15041990
   - **Telefone:** 11987654321
   - **Email:** maria@teste.com
   - **Senha:** 1234
   - **Confirmar senha:** 1234
3. Clique em **"Cadastrar"**
4. ✅ **Esperado:** Mensagem de sucesso "✅ Cadastro realizado com sucesso! Faça login."

### 11.3. Testar o login

1. Clique em **"🔑 Fazer Login"** ou acesse:
   ```
   http://localhost/en430/sistema_avaliacao/php/login
   ```
2. Preencha:
   - **Email:** maria@teste.com
   - **Senha:** 1234
3. Clique em **"Entrar"**
4. ✅ **Esperado:** Você será redirecionado para o **Painel do Estudante** mostrando seu nome

### 11.4. Testar o admin

1. Acesse:
   ```
   http://localhost/en430/sistema_avaliacao/php/admin-login
   ```
2. Digite a senha de administração: **admin_enfermagem_2026**
   > ⚠️ **Importante:** Altere esta senha em produção! (Veja seção 13)
3. ✅ **Esperado:** Painel administrativo com estatísticas do sistema

### 11.5. Testar todas as rotas

Abra cada um dos links abaixo no navegador — todos devem retornar uma página (não erro 404):

| Rota | Descrição |
|---|---|
| `http://localhost/en430/sistema_avaliacao/php/` | Página inicial |
| `http://localhost/en430/sistema_avaliacao/php/cadastro` | Cadastro |
| `http://localhost/en430/sistema_avaliacao/php/login` | Login |
| `http://localhost/en430/sistema_avaliacao/php/recuperar-acesso` | Recuperar acesso |
| `http://localhost/en430/` | Página inicial dos materiais |

---

## 12. Passo 9: Configurar URLs amigáveis (opcional)

> ⏱️ **Tempo estimado:** 5 minutos

Por padrão, o sistema funciona com URLs como:
```
http://localhost/en430/sistema_avaliacao/php/
http://localhost/en430/sistema_avaliacao/php/cadastro
http://localhost/en430/sistema_avaliacao/php/login
```

Se você quiser URLs mais curtas (ex: `http://localhost/avaliacao/`), siga este passo.

### 12.1. Criar redirecionamento no Apache

1. **Abra o arquivo de configuração do Apache:**
   - Navegue até: `C:\xampp\apache\conf\extra\`
   - Localize o arquivo: **httpd-xampp.conf**
   - **Clique com o botão direito** → Abrir com → **Bloco de Notas**

2. **Adicione estas linhas no final do arquivo:**

   ```apache
   # ─── Redirecionamento para o Sistema de Avaliação EN_430 ───
   Alias /avaliacao "C:/xampp/htdocs/en430/sistema_avaliacao/php/"
   
   <Directory "C:/xampp/htdocs/en430/sistema_avaliacao/php">
       Options -Indexes +FollowSymLinks
       AllowOverride All
       Require all granted
   </Directory>
   ```

3. **Salve o arquivo** (`Ctrl + S`) e feche

4. **Reinicie o Apache:**
   - Abra o **XAMPP Control Panel**
   - Clique em **"Stop"** ao lado de Apache
   - Clique em **"Start"** ao lado de Apache

5. **Teste:** Acesse `http://localhost/avaliacao/` — deve mostrar o sistema de avaliação!

---

## 13. Passo 10: Configurar o painel administrativo

> ⏱️ **Tempo estimado:** 2 minutos

### 13.1. Alterar a senha de administração

A senha padrão do admin é: `admin_enfermagem_2026`

Para alterá-la:

1. **Localize o arquivo de configuração:**
   ```
   C:\xampp\htdocs\en430\sistema_avaliacao\php\config.php
   ```

2. **Abra com o Bloco de Notas** (clique direito → Abrir com → Bloco de Notas)

3. **Localize a linha:**
   ```php
   define('ADMIN_SECRET', getenv('ADMIN_SECRET') ?: 'admin_enfermagem_2026');
   ```

4. **Altere a senha:**
   ```php
   define('ADMIN_SECRET', getenv('ADMIN_SECRET') ?: 'SUA_NOVA_SENHA_AQUI');
   ```

5. **Salve o arquivo** e feche

### 13.2. Funções do painel administrativo

Após fazer login no `/admin-login`, você terá acesso a:

| Funcionalidade | Localização | Descrição |
|---|---|---|
| 📊 Estatísticas | Painel inicial | Visão geral do sistema |
| 👥 Gestão de Estudantes | Seção "Estudantes" | Listar, buscar, resetar senha, excluir |
| 🔑 Resetar senha | Botão 🔑 ao lado do estudante | Gera nova senha aleatória |
| 🗑️ Excluir estudante | Botão 🗑️ ao lado do estudante | Remove estudante + avaliações |
| 🔍 Buscar | Campo de busca | Filtra por nome, email ou telefone |
| 📥 Exportar CSV | Botões de ação | Exporta dados em formato CSV |
| 📝 Questões | Seção "Questões" | Estatísticas por módulo e dificuldade |
| 🔒 Logout | Botão "🚪 Sair" | Encerra sessão administrativa |

---

## 14. Passo 11: Liberar acesso na rede local

> ⏱️ **Tempo estimado:** 5 minutos

Para que outros computadores na mesma rede possam acessar o sistema:

### 14.1. Descobrir o IP do seu computador

**Método 1 — Via Prompt de Comando:**
1. Abra o **Prompt de Comando** (Menu Iniciar → digite `cmd` → Enter)
2. Digite: `ipconfig`
3. Pressione **Enter**
4. Procure pela linha:
   ```
   Endereço IPv4 . . . . . . . . . : 192.168.X.X
   ```
   > Anote este número! Ex: `192.168.1.100`

**Método 2 — Via Configurações do Windows:**
1. Clique no ícone de **Wi-Fi** ou **Rede** na barra de tarefas
2. Clique em **"Propriedades"** da rede conectada
3. Procure por **"Endereço IPv4"**

### 14.2. Liberar a porta 80 no Firewall do Windows

Para que outros computadores possam acessar:

1. **Abra o Windows Defender Firewall:**
   - Menu Iniciar → digite `firewall`
   - Clique em **"Windows Defender Firewall com Segurança Avançada"**

2. **Criar regra de entrada:**
   - No menu esquerdo, clique em **"Regras de Entrada"**
   - No menu direito, clique em **"Nova Regra..."**

3. **Configurar a regra:**
   - **Tipo de regra:** `Porta` → Avançar
   - **Protocolo:** `TCP`
   - **Porta:** `80` (específica)
   - **Ação:** `Permitir a conexão`
   - **Perfil:** Marque todas as opções (Domínio, Particular, Público)
   - **Nome:** `Apache HTTP (porta 80)`
   - Clique em **"Concluir"**

### 14.3. Testar o acesso de outro computador

1. Em outro computador na mesma rede
2. Abra o navegador
3. Digite: `http://IP_DO_SERVIDOR/en430/sistema_avaliacao/php/`
   - Substitua `IP_DO_SERVIDOR` pelo IP que você anotou no passo 14.1
   - Exemplo: `http://192.168.1.100/en430/sistema_avaliacao/php/`

4. ✅ **Esperado:** A página inicial do sistema deve aparecer

---

## 15. Passo 12: Compartilhar com os estudantes

### 15.1. Fornecer o link de acesso

Os estudantes podem acessar o sistema de qualquer computador na rede local:

```
http://IP_DO_SERVIDOR/en430/sistema_avaliacao/php/
```

### 15.2. O que os estudantes podem fazer

| Ação | Como fazer |
|---|---|
| 📝 **Se cadastrar** | Clicar em "Cadastre-se" e preencher os dados |
| 🔑 **Fazer login** | Usar email e senha cadastrados |
| 🎯 **Fazer avaliação** | Escolher dificuldade e clicar em "Iniciar Avaliação" |
| 📊 **Ver progresso** | Clicar em "Meu Progresso" |
| 🔄 **Recuperar senha** | Clicar em "Esqueceu a senha?" |

### 15.3. Enviar instruções para os estudantes

Você pode copiar e colar este texto para enviar aos estudantes:

```
📌 SISTEMA DE AVALIAÇÃO — EN_430

Olá estudante!

O sistema de avaliação da disciplina Introdução à Enfermagem 
já está disponível!

📱 Acesse pelo navegador do seu celular ou computador:
http://SEU_IP_AQUI/en430/sistema_avaliacao/php/

📝 Primeiro acesso:
1. Clique em "Cadastre-se"
2. Preencha seus dados (nome, email, senha)
3. Faça login
4. Escolha a dificuldade e inicie a avaliação

📊 Acompanhe seu progresso por módulo!

Bons estudos! 🎓
```

---

## 16. Manutenção e Backup

### 16.1. Backup do banco de dados

O banco de dados SQLite é um único arquivo:
```
C:\xampp\htdocs\en430\sistema_avaliacao\php\avaliacao.db
```

**Para fazer backup:**
1. **Pare o Apache** no XAMPP Control Panel (botão "Stop")
2. Copie o arquivo `avaliacao.db` para uma pasta de backup
3. **Inicie o Apache** novamente (botão "Start")

**Para restaurar:**
1. **Pare o Apache**
2. Substitua o arquivo `avaliacao.db` pela cópia de backup
3. **Inicie o Apache**

### 16.2. Backup automatizado (Agendador de Tarefas)

Para fazer backup automático todos os dias:

1. **Crie o script de backup:**
   - Abra o **Bloco de Notas**
   - Copie e cole este texto:
     ```batch
     @echo off
     set DATA=%DATE:~6,4%%DATE:~3,2%%DATE:~0,2%
     set ORIGEM="C:\xampp\htdocs\en430\sistema_avaliacao\php\avaliacao.db"
     set DESTINO="C:\backups_en430\avaliacao-%DATA%.db"
     
     if not exist "C:\backups_en430\" mkdir C:\backups_en430\
     copy %ORIGEM% %DESTINO%
     echo Backup concluido: %DATA%
     ```
   - Salve como: `C:\scripts\backup_en430.bat`

2. **Configurar no Agendador de Tarefas:**
   - Menu Iniciar → digite `agendador de tarefas` → Enter
   - Clique em **"Criar Tarefa Básica..."**
   - **Nome:** `Backup EN430`
   - **Disparador:** `Diariamente` às `02:00`
   - **Ação:** `Iniciar um programa`
   - **Programa/script:** `C:\scripts\backup_en430.bat`
   - Clique em **"Concluir"**

### 16.3. Atualizar o sistema

Para atualizar o sistema com novos arquivos:

1. **Pare o Apache** (XAMPP Control Panel → Stop)
2. **Faça backup** do banco de dados (veja seção 16.1)
3. Substitua os arquivos antigos pelos novos
4. **Inicie o Apache** (Start)

> ⚠️ **CUIDADO:** Ao substituir os arquivos, não sobrescreva o `avaliacao.db` se quiser manter os dados dos estudantes!

### 16.4. Verificar logs de erro

Se algo der errado, os logs podem ajudar:

```
C:\xampp\apache\logs\error.log          → Erros do Apache
C:\xampp\apache\logs\access.log         → Acessos
C:\xampp\php\logs\php_error_log         → Erros do PHP
```

Para visualizar:
1. Abra o **Bloco de Notas**
2. Arquivo → Abrir → Navegue até a pasta de logs
3. Selecione o arquivo de log

---

## 17. Solução de Problemas

### ❌ **Problema:** Apache não inicia

**Erro:** Ao clicar em "Start" no XAMPP, nada acontece ou aparece "Error"

**Causas possíveis e soluções:**

#### Causa 1: Porta 80 ocupada por outro programa

```cmd
:: Verificar qual programa está usando a porta 80
netstat -ano | findstr :80

:: O resultado mostra o PID (número) do programa
:: Exemplo: TCP 0.0.0.0:80 0.0.0.0:0 LISTENING 1234

:: Para descobrir qual programa é:
tasklist | findstr 1234
```

**Soluções:**
- **Opção A:** Feche o programa que está usando a porta 80
  - Se for o **IIS** (Servidor Web da Microsoft): Desative-o
  - Menu Iniciar → "Ativar ou desativar recursos do Windows" → Desmarque "IIS"
- **Opção B:** Mude a porta do Apache para 8080
  - Abra `C:\xampp\apache\conf\httpd.conf`
  - Localize `Listen 80` e mude para `Listen 8080`
  - Agora o sistema será acessado em `http://localhost:8080/en430/...`

#### Causa 2: Skype usando a porta 80 ou 443

- Abra o Skype
- Ferramentas → Opções → Avançado → Conexão
- Desmarque "Usar as portas 80 e 443 como alternativas"
- Reinicie o Skype

#### Causa 3: Visual C++ Redistributable não instalado

- Baixe e instale: https://aka.ms/vs/17/release/vc_redist.x64.exe
- Reinicie o computador
- Tente iniciar o Apache novamente

---

### ❌ **Problema:** Página não encontrada (404)

**Erro:** `HTTP 404 - Not Found` ao acessar o sistema

**Causa:** Os arquivos não foram copiados para o lugar certo

**Solução:**
```cmd
:: Verifique se os arquivos existem
dir C:\xampp\htdocs\en430\sistema_avaliacao\php\

:: Deve mostrar algo como:
:: index.php
:: config.php
:: functions.php
:: etc.

:: Se a pasta estiver vazia, recopie os arquivos do projeto
```

---

### ❌ **Problema:** Erro 500 - Internal Server Error

**Erro:** Página em branco ou "500 Internal Server Error"

**Causa:** Erro no PHP ou permissão no banco

**Solução:**
```cmd
:: 1. Verificar o log de erros do PHP
type C:\xampp\php\logs\php_error_log

:: 2. Verificar permissão do banco
attrib C:\xampp\htdocs\en430\sistema_avaliacao\php\avaliacao.db
:: Se mostrar "R", remova o atributo de somente leitura:
attrib -R C:\xampp\htdocs\en430\sistema_avaliacao\php\avaliacao.db

:: 3. Verificar sintaxe dos arquivos PHP
php -l C:\xampp\htdocs\en430\sistema_avaliacao\php\index.php
```

---

### ❌ **Problema:** Página sem estilo (só texto)

**Erro:** A página aparece apenas com texto, sem cores nem organização

**Causa:** O arquivo CSS não está sendo carregado

**Solução:**
1. Abra o **Console do Navegador** (tecla `F12`)
2. Clique na aba **"Console"**
3. Veja se há erros de arquivo não encontrado (404)
4. Se houver, verifique se a pasta `assets` existe dentro de `C:\xampp\htdocs\en430\sistema_avaliacao\php\`

---

### ❌ **Problema:** "Cannot redeclare function" ao acessar páginas

**Erro:** `PHP Fatal error: Cannot redeclare function handleCadastro()`

**Causa:** Isso acontece apenas com o **servidor PHP embutido** (não com Apache)

**Solução:** Use o **Apache** (XAMPP) em vez do servidor embutido. O Apache carrega o PHP de forma diferente e não tem este problema.

---

### ❌ **Problema:** Banco de dados não tem permissão de escrita

**Erro:** `General error: 8 attempt to write a readonly database`

**Causa:** O Apache não pode escrever no arquivo do banco

**Solução:**
1. Navegue até `C:\xampp\htdocs\en430\sistema_avaliacao\php\`
2. Clique com botão direito em `avaliacao.db`
3. Propriedades → **Desmarque "Somente leitura"**
4. Segurança → Editar → **"Todos"** → Marque **"Controle total"**
5. Aplicar → OK

---

### ❌ **Problema:** Firewall bloqueando o acesso

**Erro:** Outros computadores não conseguem acessar o sistema

**Solução:** (Repita o passo 14.2)
1. Abra o Firewall do Windows
2. Verifique se a regra para a porta 80 existe e está ativa
3. Se não existir, crie a regra seguindo o passo 14.2

---

## 18. Apêndice A: Comandos Rápidos

### Para abrir o Prompt de Comando como Administrador

```cmd
Menu Iniciar → digite "cmd" → clique direito → "Executar como administrador"
```

### Comandos úteis

```cmd
:: ─── APACHE ──────────────────────────────────────────
:: Iniciar Apache
C:\xampp\apache\bin\httpd.exe -k start

:: Parar Apache
C:\xampp\apache\bin\httpd.exe -k stop

:: Reiniciar Apache
C:\xampp\apache\bin\httpd.exe -k restart

:: Testar configuração
C:\xampp\apache\bin\httpd.exe -t

:: ─── PHP ─────────────────────────────────────────────
:: Ver versão do PHP
php -v

:: Ver módulos carregados
php -m

:: Verificar sintaxe de arquivo PHP
php -l arquivo.php

:: Recriar banco de questões
php C:\xampp\htdocs\en430\sistema_avaliacao\php\scripts\recriar_questoes.php

:: ─── REDE ────────────────────────────────────────────
:: Ver IP do computador
ipconfig

:: Verificar qual programa está na porta 80
netstat -ano | findstr :80

:: ─── ACESSO AO SISTEMA ──────────────────────────────
:: Sistema de Avaliação:
:: http://localhost/en430/sistema_avaliacao/php/

:: Materiais de Estudo:
:: http://localhost/en430/
```

---

## 19. Apêndice B: Checklist de Verificação

Marque os itens abaixo conforme for verificando:

### ✅ Instalação do XAMPP

- [ ] XAMPP baixado do site oficial
- [ ] XAMPP instalado em `C:\xampp`
- [ ] Apache iniciado no XAMPP Control Panel
- [ ] `http://localhost/` abre a página do XAMPP

### ✅ Arquivos do Projeto

- [ ] Arquivos copiados para `C:\xampp\htdocs\en430\`
- [ ] Pasta `sistema_avaliacao\php` existe
- [ ] Arquivo `avaliacao.db` existe
- [ ] Banco não está como "Somente Leitura"

### ✅ Testes do Sistema

- [ ] `http://localhost/en430/sistema_avaliacao/php/` abre a página inicial
- [ ] `http://localhost/en430/sistema_avaliacao/php/cadastro` abre o cadastro
- [ ] `http://localhost/en430/sistema_avaliacao/php/login` abre o login
- [ ] É possível criar um cadastro
- [ ] É possível fazer login
- [ ] É possível iniciar uma avaliação
- [ ] `http://localhost/en430/sistema_avaliacao/php/admin-login` abre o admin

### ✅ Rede (se for compartilhar)

- [ ] IP do servidor anotado
- [ ] Porta 80 liberada no Firewall
- [ ] Testado de outro computador na rede

### ✅ Segurança

- [ ] Senha do ADMIN alterada no `config.php`
- [ ] Backup do banco agendado (opcional)

---

## 20. Apêndice C: Estrutura Completa de Arquivos

```
C:\xampp\htdocs\en430\
│
├── 📄 index.html                      ← Página inicial do projeto
├── 📄 index_estudos.html              ← Central de Estudos
│
├── 📖 Guias e Materiais HTML:
│   ├── guia_completo_estudos.html
│   ├── plano_de_estudos_20h.html
│   ├── plano_estudos_avancado.html
│   ├── resumo_uma_pagina.html
│   ├── flashcards_enfermagem.html
│   ├── mapa_mental_10_modulos.html
│   ├── cronograma_mensal.html
│   ├── casos_clinicos_pratica.html
│   ├── videos_recomendados.html
│   ├── simulado_40_questoes.html
│   ├── capa_apostila.html
│   └── Apostila_Completa_Introducao_Enfermagem.pdf
│
├── 📄 GUIA_PUBLICACAO_XAMPP_WINDOWS10.md  ← Este guia
├── 📄 README.md
│
└── 📂 sistema_avaliacao\              ← Sistema de Avaliação PHP
    └── 📂 php\
        │
        ├── 🎯 index.php               ← Front controller (roteador principal)
        ├── ⚙️ config.php              ← Configurações (senha admin, etc.)
        ├── 🔧 functions.php           ← Funções auxiliares
        ├── 📋 handlers.php            ← Controladores das rotas
        ├── 🗄️ db.php                  ← Conexão com banco SQLite
        ├── 🚀 router.php              ← Roteador para servidor embutido
        ├── 📄 .htaccess               ← Regras de URL (para Apache)
        ├── 🗄️ avaliacao.db            ← Banco SQLite (2800+ questões)
        │
        ├── 📂 assets\                 ← Arquivos estáticos
        │   ├── 📂 css\
        │   │   └── 🎨 style.css       ← Estilos do sistema
        │   └── 📂 js\
        │       └── ⚡ app.js           ← JavaScript do sistema
        │
        ├── 📂 views\                  ← Páginas do sistema
        │   ├── index.php              ← Página inicial
        │   ├── cadastro.php           ← Cadastro de estudantes
        │   ├── login.php              ← Login
        │   ├── painel.php             ← Painel do estudante
        │   ├── avaliacao.php          ← Tela de avaliação
        │   ├── resultado.php          ← Resultado da avaliação
        │   ├── progresso.php          ← Progresso por módulo
        │   ├── recuperar_acesso.php   ← Recuperação de senha
        │   ├── admin.php              ← Painel administrativo
        │   └── admin_login.php        ← Login do admin
        │
        ├── 📂 scripts\                ← Scripts de manutenção
        │   ├── recriar_questoes.php   ← Recria banco com questões
        │   └── questions_data.php     ← Base de questões
        │
        └── 📂 tests\                  ← Testes automatizados (PHPUnit)
            ├── bootstrap.php
            ├── DatabaseTest.php
            ├── AuthTest.php
            ├── AvaliacaoTest.php
            └── IntegrationTest.php
```

---

## 📌 Resumo dos comandos essenciais

```cmd
:: ─── 1. INICIAR O APACHE ──────────────────────────────
:: Abrir XAMPP Control Panel e clicar em "Start" no Apache
:: Ou via linha de comando (Admin):
C:\xampp\apache\bin\httpd.exe -k start

:: ─── 2. ACESSAR O SISTEMA ─────────────────────────────
:: No navegador do servidor:
http://localhost/en430/sistema_avaliacao/php/

:: Em outros computadores da rede:
http://192.168.X.X/en430/sistema_avaliacao/php/

:: ─── 3. ACESSAR O ADMIN ───────────────────────────────
http://localhost/en430/sistema_avaliacao/php/admin-login
:: Senha padrão: admin_enfermagem_2026

:: ─── 4. PARAR O APACHE ────────────────────────────────
:: XAMPP Control Panel → "Stop"
:: Ou: C:\xampp\apache\bin\httpd.exe -k stop
```

---

*Guia de instalação para Windows 10 + XAMPP elaborado para a disciplina **Introdução à Enfermagem (EN_430)***  
*Curso de Enfermagem — EAD/Subsequente • Julho 2026*  
*PHP 8 + Apache + SQLite • Sistema reformulado para PHP a partir do original Python/Flask*

**🎯 Público-alvo:** Iniciantes em administração de servidores  
**📚 Conhecimento necessário:** Saber usar o Explorador de Arquivos e copiar/colar
