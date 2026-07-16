# 📝 Sistema de Avaliação — Introdução à Enfermagem

Sistema web para avaliação de múltipla escolha com **+2000 questões** 
(2055 no banco) distribuídas pelos **10 módulos** da disciplina 
**Introdução à Enfermagem (EN_430)**, com **3 níveis de dificuldade**: 
🟢 Fácil, 🟡 Médio e 🔴 Difícil.

> **🔄 Reformulação completa:** O sistema foi migrado de Python/Flask para **PHP 8 + SQLite**, 
> com todos os artefatos Python originais compactados em `artefatos_python.7z`.

---

## 📋 Estrutura do Banco de Dados

### Tabela: `estudantes`
| Campo | Tipo | Descrição |
|-------|------|-----------|
| `id` | INTEGER (PK) | Identificador único |
| `nome` | TEXT | Nome completo do estudante |
| `telefone` | TEXT | Telefone (opcional) |
| `email` | TEXT (UNIQUE) | Email de acesso |
| `senha_hash` | TEXT | Hash bcrypt da senha |
| `data_cadastro` | TIMESTAMP | Data de registro |

### Tabela: `questoes`
| Campo | Tipo | Descrição |
|-------|------|-----------|
| `id` | INTEGER (PK) | Identificador único |
| `modulo` | INTEGER (1-10) | Módulo da disciplina |
| `texto` | TEXT | Enunciado da questão |
| `opcao_a` | TEXT | Alternativa A |
| `opcao_b` | TEXT | Alternativa B |
| `opcao_c` | TEXT | Alternativa C |
| `opcao_d` | TEXT | Alternativa D |
| `resposta` | TEXT ('A','B','C','D') | Alternativa correta |
| `dificuldade` | TEXT ('Fácil','Médio','Difícil') | Nível de dificuldade |

### Tabela: `avaliacoes`
| Campo | Tipo | Descrição |
|-------|------|-----------|
| `id` | INTEGER (PK) | Identificador único |
| `estudante_id` | INTEGER (FK) | Referência ao estudante |
| `data_inicio` | TIMESTAMP | Início da avaliação |
| `data_fim` | TIMESTAMP | Finalização |
| `total_questoes` | INTEGER | Número de questões |
| `questoes_ids` | TEXT (JSON) | Lista de IDs das questões |
| `respostas` | TEXT (JSON) | Respostas do estudante |
| `resultado` | TEXT (JSON) | Resultado detalhado |
| `pontuacao` | INTEGER | Número de acertos |
| `status` | TEXT | 'em_andamento' ou 'concluido' |

### View: `vw_progresso_estudante`
Visão consolidada com total de avaliações, média de acertos e melhor desempenho por estudante.

### Índices
- `idx_questoes_modulo` — Otimiza consultas por módulo
- `idx_avaliacoes_estudante` — Otimiza consultas por estudante

---

## ⚙️ Requisitos

- **PHP 8.1+** (recomendado 8.3+) com extensões: `pdo_sqlite`, `mbstring`, `session`
- **SQLite 3.x** (embutido no PHP)
- **Composer** (para desenvolvimento/testes com PHPUnit)
- Servidor web: **Apache** (com mod_rewrite) ou **PHP built-in server**

## 🚀 Instalação e Execução

### Opção 1: Servidor PHP embutido (desenvolvimento/teste)

```bash
# 1. Ir para o diretório PHP
cd sistema_avaliacao/php

# 2. Recriar banco de dados (2055 questões balanceadas)
php scripts/recriar_questoes.php

# 3. Iniciar servidor embutido
php -S 127.0.0.1:8080 -t . router.php

# 4. Acessar no navegador
# http://127.0.0.1:8080
```

### Opção 2: Apache com mod_rewrite

```bash
# Configurar VirtualHost apontando para sistema_avaliacao/php/
# O .htaccess incluído faz o roteamento automático
```

### Opção 3: Usando o assistente de instalação

```bash
# Linux
bash instalar_publicar_linux.sh

# Windows
.\instalar_publicar_windows.ps1
# ou
instalar_publicar_windows.bat
```

---

## 🔐 Recuperação de Acesso

O sistema utiliza **Nome + Telefone** cadastrados para recuperação de senha.
Não é necessário configurar servidor SMTP ou serviço de email.

1. Na tela de login, clique em "🔐 Esqueci minha senha"
2. Informe seu **Nome completo** e **Telefone** cadastrados
3. Se os dados conferirem, seu email de acesso será exibido
4. Defina uma nova senha
5. Faça login com seu email e a nova senha

## 🧑‍🎓 Como Usar

### 1. Cadastro
- Acesse a página inicial e clique em "Cadastre-se"
- Informe nome, email e senha
- O cadastro é instantâneo

### 2. Login
- Faça login com email e senha cadastrados
- Acesse o painel do estudante

### 3. Realizar Avaliação
- No painel, clique em "Nova Avaliação"
- **20 questões** são selecionadas aleatoriamente do banco
- O sistema evita repetir questões de avaliações anteriores
- Responda cada questão e clique "Finalizar Avaliação"

### 4. Resultado
- Nota imediata com percentual de acertos
- Feedback visual por questão (correta/incorreta)
- Gabarito detalhado com a resposta correta

### 5. Acompanhamento
- **Histórico**: Todas as avaliações realizadas
- **Progresso**: Desempenho por módulo com gráficos de barras
- Estatísticas: média geral, melhor nota, avaliações pendentes

---

## 📊 Distribuição das Questões

| Módulo | Tema | Questões |
|:------:|------|:--------:|
| 1 | 🧩 Teoria das Necessidades Básicas (Wanda Horta) | 510 |
| 2 | 🔄 SAE — Sistematização da Assistência de Enfermagem | 250 |
| 3 | 📝 Anotação de Enfermagem + Terminologias | 250 |
| 4 | 💊 Administração de Medicamentos — Vias Enterais | 155 |
| 5 | 💉 Administração de Medicamentos — Vias Parenterais | 160 |
| 6 | 🧼 Assepsia, Antissepsia e Higiene do Paciente | 165 |
| 7 | 🩹 Curativos, Feridas e Coberturas | 165 |
| 8 | 🫁 Oxigenoterapia + Cateterismos | 150 |
| 9 | 📏 Exames, Coletas, Medidas, Crioterapia e Termoterapia | 150 |
| 10 | 📋 Admissão, Alta, Contenção e Processo Pós-Morte | 100 |
| | **Total** | **2055** |

> **Níveis de dificuldade:** 🟢 Fácil (53,5%), 🟡 Médio (23,1%), 🔴 Difícil (23,4%).
> As questões são multiplicadas por variações textuais para enriquecer o banco.

---

## 🔒 Segurança

- **Senhas**: Armazenadas com **bcrypt** (cost 12)
- **Sessão**: Gerenciada por `$_SESSION` do PHP com tokens CSRF
- **SQL Injection**: Todas as queries usam prepared statements (PDO)
- **CSRF**: Tokens de proteção em todos os formulários
- **Acesso**: Estudante só vê suas próprias avaliações

---

## ✅ Testes Automatizados

O sistema possui **61 testes PHPUnit** com **199 asserções**:

```bash
cd sistema_avaliacao/php
composer install
./vendor/bin/phpunit
```

| Categoria | Arquivo | Testes |
|-----------|---------|:------:|
| Banco de Dados | `tests/DatabaseTest.php` | 11 |
| Autenticação | `tests/AuthTest.php` | 20 |
| Avaliações | `tests/AvaliacaoTest.php` | 16 |
| Integração HTTP | `tests/IntegrationTest.php` | 14 |
| **Total** | | **61 ✅** |

---

## 📁 Estrutura de Arquivos

```
sistema_avaliacao/php/
├── index.php                 # Front controller / Router
├── router.php                # Roteador para PHP built-in server
├── .htaccess                 # Rewrite para Apache
├── config.php                # Configurações do sistema
├── db.php                    # Conexão com banco SQLite (PDO)
├── functions.php             # Funções auxiliares (auth, CSRF, flash)
├── composer.json             # Dependências PHP
├── phpunit.xml               # Configuração PHPUnit
├── avaliacao.db              # Banco SQLite (gerado)
├── assets/
│   ├── css/
│   │   └── style.css         # Estilos do sistema
│   └── js/
│       └── app.js            # JavaScript do sistema
├── views/
│   ├── index.php             # Página inicial
│   ├── cadastro.php          # Cadastro de estudante
│   ├── login.php             # Login
│   ├── painel.php            # Painel do estudante
│   ├── avaliacao.php         # Página de avaliação
│   ├── resultado.php         # Resultado da avaliação
│   ├── progresso.php         # Progresso por módulo
│   ├── recuperar_acesso.php  # Recuperação de senha
│   ├── admin.php             # Administração
│   └── admin_login.php       # Login do administrador
├── scripts/
│   ├── migrate.php           # Migração inicial do banco
│   ├── recriar_questoes.php  # Recria questões (+2000)
│   └── questions_data.php    # Banco de questões fonte
└── tests/
    ├── bootstrap.php          # Setup de testes
    ├── DatabaseTest.php      # Testes de banco
    ├── AuthTest.php          # Testes de autenticação
    ├── AvaliacaoTest.php     # Testes de avaliação
    └── IntegrationTest.php   # Testes de integração HTTP
```

---

## 🌐 Publicação Web (Apache)

Para publicar o sistema para acesso dos estudantes via Apache, consulte:

- **📄 `GUIA_PUBLICACAO_APACHE.md`** — Guia detalhado para servidores Linux
- **📄 `GUIA_PUBLICACAO_APACHE_WINDOWS.md`** — Guia para servidores Windows

### Configuração rápida Apache

```apache
<VirtualHost *:80>
    DocumentRoot /var/www/enfermagem/sistema_avaliacao/php
    <Directory /var/www/enfermagem/sistema_avaliacao/php>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

---

## 📚 Materiais Relacionados

Os materiais de estudo que complementam este sistema estão na raiz do projeto:
- 📖 `guia_completo_estudos.html` — Guia completo de estudos
- 🃏 `flashcards_enfermagem.html` — Flashcards interativos
- 🧠 `mapa_mental_10_modulos.html` — Mapa mental
- 🗺️ `index_estudos.html` — Central de Estudos
- 📘 `plano_de_estudos_20h.html` — Plano de estudos 20h

---

*Sistema desenvolvido para a disciplina Introdução à Enfermagem (EN_430)*
*Curso de Enfermagem — EAD/Subsequente • Julho 2026*
*PHP 8 + SQLite • Reformulado a partir do original Python/Flask*
