# 📋 Planejamento — Formulário de Registro de Colaboradores

**Objeto:** página pública de manifestação de interesse para gestores educacionais, coordenadores,
professores e demais colaboradores (revisores técnicos, designers instrucionais, QA/desenvolvedores),
com registro controlado das informações que alimentam o **Termo de Confidencialidade e Sigilo (TCS)**
e, quando aplicável, o **Termo de Adesão do Professor Validador**.

| | |
|---|---|
| **Versão** | 1.0 (planejamento) |
| **Data** | 22 de novembro de 2026 |
| **Natureza** | Plano de produto, dados e conformidade — **não** é documento jurídico |
| **Documentos-fonte** | [RM-01](../GOVERNANCA_PROGRESSO.md) · [TCS](../juridico/TERMO_CONFIDENCIALIDADE_SIGILO.md) · [Adesão](../juridico/TERMO_ADESAO_PROFESSOR_VALIDADOR.md) · [Política de Privacidade](../juridico/POLITICA_DE_PRIVACIDADE.md) |
| **Estado (RM-01)** | 🟨 Aguardando aprovação do outorgante |

> **Resumo da decisão de arquitetura:** o formulário público **não cria conta e não coleta CPF, RG
> ou endereço**. Ele registra uma **candidatura** com os dados necessários à triagem. Os dados
> qualificatórios do termo (CPF, RG, endereço, estado civil, nacionalidade) são coletados em uma
> **etapa privada, autenticada por link com token**, depois da triagem — princípio do mínimo
> necessário (LGPD, art. 6º, III; TCS, cláusula 6, "c").

---

## 1. Objetivo e limites

### 1.1. Objetivo

Ter **um único canal** pelo qual um interessado se apresenta, é triado e tem seus dados convertidos
em termo pronto para assinatura — com histórico auditável de cada etapa, sem planilha paralela e sem
retrabalho de digitação.

### 1.2. O que o formulário resolve

| Problema atual | Como o planejamento resolve |
|---|---|
| Termo preenchido à mão a partir de conversas por WhatsApp/e-mail | Dados coletados estruturados; termo gerado do banco |
| Sem controle de quem se interessou, quem foi aprovado e quem assinou | Tabela `colaboradores` + `colaborador_termos` com estados |
| Risco de receber CPF/RG em canal aberto | Dados qualificatórios só na etapa privada com token |
| Dificuldade de provar quando e em que versão o colaborador aderiu | Registro de aceite com data, versão e hash do documento |
| Sem visão de prazos (parecer, revalidação, encerramento) | Painel de conformidade com alertas |

### 1.3. O que **não** está no escopo

- Criar conta de acesso ao sistema de avaliação para o colaborador (o acesso é provisionado à parte,
  após assinatura).
- Contrato de trabalho, vínculo empregatício ou pagamento — o TCS já declara autonomia (cláusula 17).
- Verificação automática de registro profissional em conselho (COREN/CRM) — não há API pública;
  a conferência é manual, com comprovação anexada fora do repositório.
- Repositório de documentos assinados — eles continuam **fora do versionamento**, em armazenamento
  privado (TCS, orientação de uso, item 4).

---

## 2. Princípios de projeto

| ID | Princípio | Consequência prática |
|:--:|-----------|----------------------|
| F1 | **Minimização de dados** | A rota pública não pede CPF, RG, endereço nem data de nascimento |
| F2 | **Uma fonte de verdade** | Dados coletados aqui alimentam TCS e Adesão; nada é redigitado |
| F3 | **Nenhum dado pessoal no repositório público** | O registro de emissão guarda número do termo + iniciais, nunca nome completo/CPF |
| F4 | **Estado é vocabulário fechado** | Os 6 estados da RM-01, com rótulos próprios deste domínio (seção 4) |
| F5 | **Sem conta antes do termo** | Candidatura ≠ credencial; acesso só existe depois de assinatura registrada |
| F6 | **Auditoria de cada transição** | Toda mudança de estado vai para `admin_log` com autor, de/para e motivo |
| F7 | **Sem dependência nova** | PHP + SQLite + CSS existentes; geração de termo por template e `str_replace` |

---

## 3. Perfis de colaborador e o que cada um assina

| Perfil | Exemplos | TCS | Adesão do Validador | Acesso típico |
|---|---|:--:|:--:|---|
| **Gestor educacional** | direção de escola, coordenação de curso técnico | obrigatório | não | leitura de painéis e relatórios |
| **Coordenador pedagógico** | coordenação de área, supervisão de estágio | obrigatório | quando emite parecer | leitura + pareceres |
| **Professor validador** | enfermeiro docente, técnico em enfermagem experiente | obrigatório | **obrigatório** | leitura + escrita de pareceres |
| **Revisor técnico** | enfermeiro assistencial, especialista de conteúdo | obrigatório | quando emite parecer | leitura + escrita de conteúdo |
| **Designer instrucional** | revisão de linguagem, acessibilidade, mídia | obrigatório | não | leitura + escrita de conteúdo |
| **QA / desenvolvedor** | testes de usabilidade e segurança | obrigatório | não | acesso técnico (leitura de código, ambiente de teste) |

Regra: **todo** colaborador assina o TCS; a Adesão é acrescentada quando houver emissão de parecer
de validação técnica ou pedagógica.

---

## 4. Ciclo de vida e estados (vocabulário único da RM-01)

| Símbolo | Estado no domínio | Critério de entrada | Próximo passo |
|:--:|-------------------|---------------------|---------------|
| ⬜ | **Candidatura recebida** | Formulário enviado e validado | Triagem |
| 🟨 | **Em triagem** | Candidatura íntegra, aguardando decisão do outorgante | Aprovar/recusar |
| 🟦 | **Dados completos** | Link privado preenchido (CPF, RG, endereço, escopo) | Emitir termo |
| 🟪 | **Termo emitido** | TCS (e Adesão, se validador) gerado e numerado | Assinatura |
| ✅ | **Ativo** | Assinatura registrada + acessos concedidos | Acompanhamento até encerramento |
| ⛔ | **Bloqueado** | Impedimento: registro profissional inativo, conflito de interesses não sanado, recusa | Comunicação e arquivamento |

Transições terminais fora do fluxo principal: **recusado** (candidatura arquivada, com motivo) e
**encerrado** (Anexo II de devolução/eliminação registrado, acessos revogados). Ambos preservam a
linha de histórico — nunca se apaga o registro (RM-01, P4).

---

## 5. Jornada do colaborador

```
1. Origem: link divulgado (GitHub, indicação, evento)
        ↓
2. /seja-colaborador  →  candidatura (sem CPF) + protocolo na tela
        ↓
3. Triagem pelo outorgante (painel admin)  →  aprovar / recusar
        ↓
4. E-mail com link privado (token, validade 7 dias)
        ↓
5. /colaborador/dados/{token}  →  CPF, RG, endereço, escopo, nível de acesso
        ↓
6. Geração do TCS (e Adesão) pré-preenchido + número EN430-COL-AAAA-NNNN
        ↓
7. Assinatura (eletrônica registrada no sistema ou duas vias)  →  estado ✅
        ↓
8. Acessos concedidos + registro no painel de conformidade
        ↓
9. Acompanhamento: prazos, pareceres, revalidação anual do registro profissional
        ↓
10. Encerramento: revogação de acessos + Anexo II
```

---

## 6. Estrutura da página pública

**Rota proposta:** `/seja-colaborador` (GET mostra, POST envia). Pública, sem sessão.
**Rota de confirmação:** `/seja-colaborador/enviado` (exibe protocolo; não reenvia).

```
┌──────────────────────────────────────────────────────────────┐
│ EN_430 · Participe                    Política · Voltar      │
├──────────────────────────────────────────────────────────────┤
│ H1  Participe da validação do EN_430                         │
│ Intro (3 linhas): projeto de apoio ao estudo, sem certificar; │
│ buscamos quem revise conteúdo, teste o sistema e avalie a     │
│ experiência dos estudantes.                                   │
│                                                              │
│ ① Quem é você                                                │
│    nome · e-mail · telefone (opc) · perfil · profissão        │
│    registro profissional (condicional) · instituição · cidade │
│                                                              │
│ ② Como pode contribuir                                       │
│    áreas de interesse (múltipla) · módulos · disponibilidade  │
│                                                              │
│ ③ Experiência                                                │
│    resumo (até 2.000 caracteres) · como soube do projeto      │
│                                                              │
│ ④ Conflito de interesses                                     │
│    sim/não + descrição (obrigatória se sim)                   │
│                                                              │
│ ⑤ Consentimento                                              │
│    ☐ autorizo o tratamento dos dados desta candidatura...     │
│    ☐ declaro ciência de que o projeto não certifica           │
│                                                              │
│ [ Enviar candidatura ]                                       │
│ Nota: enviar não cria conta nem garante participação.        │
│ Resposta em até 10 dias úteis · privacidade@...              │
└──────────────────────────────────────────────────────────────┘
```

### 6.1. Diretrizes de formulação (UX e redação)

| Diretriz | Aplicação |
|---|---|
| **Blocos curtos com título numerado** | 5 blocos; nenhum com mais de 7 campos |
| **Progressão do fácil ao sensível** | identificação → contribuição → experiência → conflito → consentimento |
| **Campos condicionais** | registro profissional aparece só para perfis clínicos/pedagógicos; descrição do conflito só se "sim" |
| **Linguagem direta (2ª pessoa)** | "Você vai revisar…", "Informe seu registro ativo no conselho" |
| **Transparência de finalidade** | Cada bloco diz para que o dado será usado, em uma linha |
| **Sem promessa de vaga** | Texto deixa claro que o envio não garante participação nem remuneração |
| **Acessibilidade** | `label` associado, `fieldset/legend` nos grupos, `aria-describedby` nos erros, alvo de toque ≥ 44 px, contraste AA, nenhum indicador só por cor |
| **Mobile first** | Uma coluna até 640 px; teclado numérico em telefone; sem máscara que atrapalhe colar |
| **Anti-spam sem captcha de terceiro** | Campo honeypot oculto + tempo mínimo de preenchimento (3 s) + limite por IP (5/h) |
| **Preservação em erro** | Reexibe o formulário com os valores, sem perder o que foi digitado |

### 6.2. Textos prontos

| Elemento | Texto |
|---|---|
| **H1** | Participe da validação do EN_430 |
| **Intro** | O EN_430 é um projeto de apoio ao estudo e à formação técnica em Enfermagem. Não emitimos certificados nem habilitação profissional. Buscamos educadores e profissionais de saúde para revisar conteúdo, testar o sistema de avaliação e registrar observações que melhorem a experiência dos estudantes. |
| **Consentimento (LGPD)** | Autorizo o tratamento dos dados informados nesta candidatura (nome, e-mail, telefone, perfil profissional, instituição e experiência) para análise de participação no projeto, nos termos da Política de Privacidade. Posso revogar este consentimento a qualquer momento pelo canal privacidade@en430.appservice.tec.br. |
| **Ciência do escopo** | Declaro ciência de que o projeto é apoio ao estudo, não substitui formação regulamentada e não confere certificação ou habilitação profissional. |
| **Confirmação** | ✅ Candidatura recebida. Protocolo **EN430-COL-2026-0001**. Guarde este número. A triagem ocorre em até 10 dias úteis; se aprovada, você receberá um link privado para completar os dados do termo de confidencialidade. |
| **Erro de duplicidade** | Já existe uma candidatura com este e-mail em análise. Escreva para privacidade@en430.appservice.tec.br se precisar atualizar informações. |

---

## 7. Especificação dos campos

### 7.1. Etapa pública — candidatura

| # | Campo | Tipo / controle | Obrig. | Validação | Uso |
|:--:|---|---|:--:|---|---|
| 1 | `nome` | texto | sim | 3–120 caracteres, sem dígitos isolados | identificação |
| 2 | `email` | e-mail | sim | formato válido, normalizado em minúsculas, único entre candidaturas não arquivadas | contato e login futuro |
| 3 | `telefone` | texto com máscara | não | 10–11 dígitos | contato rápido |
| 4 | `perfil` | seleção | sim | lista fechada (seção 3) | rota de triagem e termos aplicáveis |
| 5 | `profissao` | texto | sim | 3–80 caracteres | qualificação no termo |
| 6 | `registro_profissional` | texto (conselho, número, UF) | condicional | obrigatório se perfil clínico/pedagógico; formato `COREN-PE 123456` | habilitação (TCS, 9.2) |
| 7 | `registro_ativo` | checkbox de declaração | condicional | — | declaração de registro ativo |
| 8 | `instituicao` | texto | não | até 120 caracteres | contexto e conflito de interesses |
| 9 | `cidade_uf` | texto | sim | 3–80 caracteres | territorialidade e fuso |
| 10 | `areas_interesse` | múltipla escolha | sim (≥1) | validação técnica, revisão pedagógica, revisão de itens, testes de usabilidade, acessibilidade, infraestrutura, documentação | escopo (TCS, Anexo I) |
| 11 | `modulos` | múltipla escolha | não | módulos 1–4 ou "todos" | designação de frente |
| 12 | `disponibilidade` | seleção | sim | até 2 h · 3–5 h · 6–10 h · mais de 10 h (semanais) | planejamento de carga |
| 13 | `experiencia` | textarea | sim | 80–2.000 caracteres | triagem técnica |
| 14 | `como_soube` | seleção | não | indicação, GitHub, LinkedIn, evento, busca, outro | origem do canal |
| 15 | `conflito_interesses` | rádio sim/não | sim | — | TCS, cláusula 11.1 |
| 16 | `conflito_descricao` | textarea | condicional | 10–500 caracteres se "sim" | análise do outorgante |
| 17 | `consentimento` | checkbox | sim | precisa ser marcado | base do tratamento da candidatura |
| 18 | `ciencia_escopo` | checkbox | sim | precisa ser marcado | alinhamento ético |
| 19 | `_hp` (honeypot) | texto oculto | — | deve ficar vazio | anti-spam |
| 20 | `_t` (carimbo) | oculto | — | ≥ 3 s desde a renderização | anti-spam |

### 7.2. Etapa privada — dados do termo (link com token)

| # | Campo | Tipo | Obrig. | Destino |
|:--:|---|---|:--:|---|
| 21 | `nacionalidade` | texto | sim | TCS 1.2 |
| 22 | `estado_civil` | seleção | sim | TCS 1.2 |
| 23 | `cpf` | texto | sim | TCS 1.2, 21 (validação de dígitos verificadores) |
| 24 | `rg` + `rg_orgao` | texto | sim | TCS 1.2 |
| 25 | `data_nascimento` | data | sim | maioridade (TCS 16, "a") e qualificação |
| 26 | `endereco` | CEP + logradouro + número + complemento + bairro + cidade + UF | sim | TCS 1.2 e foro (20.2) |
| 27 | `funcao_projeto` | texto (sugerido pelo perfil, editável pelo admin) | sim | TCS 1.2 e Anexo I |
| 28 | `frente_modulo` | texto/seleção | sim | Anexo I |
| 29 | `data_inicio` | data | sim | Anexo I e vigência (13.1) |
| 30 | `nivel_acesso` | seleção | sim | Anexo I (leitura / leitura e escrita / administração) |
| 31 | `sistemas` | múltipla escolha | sim | Anexo I (repositório, painel, banco, ambiente de IA, outros) |
| 32 | `acessa_dados_pessoais` | rádio + quais | sim | Anexo I e deveres LGPD (7.3) |
| 33 | `remuneracao` | rádio (voluntária / valor) | sim | TCS 8.4 e Anexo I |
| 34 | `restricoes` | textarea | não | Anexo I |
| 35 | `testemunhas` | nome + CPF (2) | não nesta etapa | coletadas na assinatura (TCS 21) |

**Nunca coletar:** dados de saúde do próprio colaborador, foto de documento, dados bancários na
etapa pública, currículo com dados de terceiros.

---

## 8. Regras de negócio

| ID | Regra | Comportamento |
|:--:|-------|---------------|
| R1 | E-mail único entre candidaturas ativas | Erro amigável; permite reenvio se a anterior foi arquivada |
| R2 | Perfil clínico exige registro profissional + declaração de registro ativo | Bloqueia envio |
| R3 | Menor de 18 anos | Candidatura aceita apenas com representante legal; sinalizada para triagem |
| R4 | Conflito de interesses declarado | Candidatura não é recusada automaticamente; fica marcada para decisão do outorgante |
| R5 | Token privado | 32 bytes aleatórios, guardado apenas como hash; validade de 7 dias; uso único para gravar dados, reutilizável para consulta até a assinatura |
| R6 | CPF | validado por dígito verificador e único entre colaboradores |
| R7 | Emissão do termo | só com todos os campos obrigatórios de 7.2 preenchidos |
| R8 | Numeração | `EN430-COL-AAAA-NNNN` sequencial por ano; gravada no termo e no registro |
| R9 | Assinatura | registra data/hora, versão do termo, hash SHA-256 do documento e hash do IP; sem armazenar IP em claro |
| R10 | Ativação de acesso | somente após assinatura registrada (estado ✅); revogação no encerramento |
| R11 | Recusa | exige motivo; candidatura vai para arquivo e o titular é informado pelo canal de privacidade |
| R12 | Exclusão a pedido do titular | dados da candidatura eliminados; registro contábil do termo mantido apenas se houver obrigação legal, com justificativa |

---

## 9. Modelo de dados

Duas tabelas novas no mesmo SQLite do sistema (volume `/data`), criadas por
`ensureSchemaUpgrades()` — mesmo padrão idempotente já usado para o consentimento.

```sql
CREATE TABLE IF NOT EXISTS colaboradores (
    id                     INTEGER PRIMARY KEY AUTOINCREMENT,
    numero                 TEXT UNIQUE,              -- EN430-COL-2026-0001
    nome                   TEXT NOT NULL,
    email                  TEXT NOT NULL,
    telefone               TEXT,
    perfil                 TEXT NOT NULL,            -- gestor | coordenador | professor | revisor | designer | qa
    profissao              TEXT,
    registro_profissional  TEXT,
    registro_uf            TEXT,
    registro_ativo         INTEGER DEFAULT 0,
    instituicao            TEXT,
    cidade_uf              TEXT,
    areas_interesse        TEXT,                     -- JSON
    modulos_interesse      TEXT,                     -- JSON
    disponibilidade        TEXT,
    experiencia            TEXT,
    como_soube             TEXT,
    conflito_interesses    INTEGER NOT NULL DEFAULT 0,
    conflito_descricao     TEXT,
    consentimento_em       TIMESTAMP,
    consentimento_versao   TEXT,
    status                 TEXT NOT NULL DEFAULT 'candidatura',
    motivo_decisao         TEXT,
    token_hash             TEXT,                     -- etapa privada
    token_expira_em        TIMESTAMP,
    cpf                    TEXT UNIQUE,
    rg                     TEXT,
    rg_orgao               TEXT,
    nacionalidade          TEXT,
    estado_civil           TEXT,
    data_nascimento        TEXT,
    endereco               TEXT,                     -- JSON estruturado
    funcao_projeto         TEXT,
    criado_em              TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em          TIMESTAMP
);

CREATE TABLE IF NOT EXISTS colaborador_termos (
    id                INTEGER PRIMARY KEY AUTOINCREMENT,
    colaborador_id    INTEGER NOT NULL REFERENCES colaboradores(id),
    tipo              TEXT NOT NULL,                 -- TCS | ADESAO
    versao            TEXT NOT NULL,
    numero            TEXT UNIQUE,                   -- EN430-TCS-2026-0001
    escopo            TEXT,                          -- JSON do Anexo I
    hash_documento    TEXT,
    gerado_em         TIMESTAMP,
    assinado_em       TIMESTAMP,
    assinatura_origem TEXT,                          -- sistema | duas_vias | gov.br
    situacao          TEXT NOT NULL DEFAULT 'emitido',
    encerrado_em      TIMESTAMP,
    anexo_ii_em       TIMESTAMP
);
```

Decisões de armazenamento:

- **`admin_log`** continua sendo o log único (ação `colaborador_*`), evitando painel paralelo (RM-01, seção 3).
- **Nada de CPF/RG em CSV exportado por padrão**; exportação completa exige confirmação explícita e fica auditada.
- O **termo assinado em PDF** não entra no banco nem no repositório: guarda-se o hash e o local privado de arquivo.

---

## 10. Gestão no painel administrativo

Nova seção **"Colaboradores"** dentro do painel existente (`/admin`), com as demais seções.

### 10.1. Lista e filtros

Colunas: número, nome (abreviado), perfil, áreas, estado, data da candidatura, prazo em aberto.
Filtros: estado, perfil, área de interesse, módulo, período, "somente com pendência de prazo".

### 10.2. Ficha do colaborador

| Bloco | Ações |
|---|---|
| **Candidatura** | ver dados, aprovar, recusar com motivo, arquivar |
| **Etapa privada** | gerar/reenviar link com token, ver se foi preenchida, expirar link |
| **Termos** | gerar TCS, gerar Adesão (se validador), baixar `.md` pré-preenchido, registrar assinatura, anexar nº do exemplar físico |
| **Acessos** | registrar concessão e revogação (data, responsável, sistemas) |
| **Conformidade** | registro profissional (validade/conferência), conflito de interesses, treinamento LGPD, próximo marco |
| **Histórico** | linha do tempo com todas as transições, autor e motivo |

### 10.3. Painel de conformidade educacional

Indicadores (todos com fonte e data, conforme RM-01, P5):

| Indicador | Fórmula |
|---|---|
| Candidaturas por estado | contagem por `status` / total |
| Conversão candidatura → termo assinado | ativos / candidaturas recebidas |
| Tempo médio de triagem | média(candidatura → decisão) em dias |
| Tempo médio de emissão | média(decisão → termo emitido) |
| Termos ativos e a vencer | ativos; revalidação de registro em ≤ 30 dias |
| Pareceres em atraso | prazo 15 dias corridos (Adesão, cláusula 9) |
| Encerramentos sem Anexo II | pendências 🟪 |
| Acessos concedidos sem termo assinado | deve ser **zero** (alerta) |

---

## 11. Geração do termo

1. Template Markdown canônico (`docs/juridico/TERMO_CONFIDENCIALIDADE_SIGILO.md`) é lido do disco.
2. Placeholders `[____]` são substituídos pelos valores do banco (mapa no Anexo B).
3. Campos não aplicáveis recebem traço; o Anexo I é montado a partir do escopo gravado.
4. Numeração `EN430-COL-AAAA-NNNN` é atribuída no momento da geração e gravada.
5. Saída: `.md` para conferência e impressão pelo navegador (sem biblioteca de PDF); hash SHA-256
   do texto final é gravado para provar integridade na assinatura.
6. Assinatura eletrônica registrada no sistema (MP 2.200-2/2001, art. 10, § 2º) ou duas vias
   físicas; nos dois casos o estado vai para ✅ e o registro é auditado.

> **Decisão pendente (D5):** aceitar assinatura eletrônica simples registrada no sistema ou exigir
> assinatura via gov.br. A primeira é suficiente para o porte do projeto; a segunda tem custo de
> onboarding.

---

## 12. LGPD e segurança

| Tema | Decisão |
|---|---|
| **Base legal da candidatura** | consentimento (art. 7º, I) para o formulário; procedimentos preliminares ao contrato (art. 7º, V) após a triagem |
| **Base legal dos dados do termo** | execução de contrato (art. 7º, V) + obrigação legal/regulatória (art. 7º, II) |
| **Dados sensíveis** | não são coletados do colaborador; os dados de saúde de estudantes que ele acessa são tratados sob as cláusulas 7.3 a 7.5 do TCS |
| **Retenção** | candidaturas não aprovadas: **12 meses**; candidaturas aprovadas e termos: **5 anos** após o encerramento; logs: **6 meses** (Marco Civil) |
| **Direitos do titular** | canal privacidade@; resposta em 15 dias; exclusão dos dados de candidatura mediante pedido |
| **Acesso interno** | hoje o painel admin usa senha única (`ADMIN_SECRET`) — **insuficiente** para CPF/RG. Ver risco R2 e fase F4 |
| **Segurança da página** | CSRF na etapa privada, honeypot, limite por IP, token com hash e validade, nenhum dado sensível em URL |
| **Registro público** | `docs/juridico/README.md` lista número do termo, iniciais e função — nunca nome completo, CPF ou endereço |
| **Incidente** | comunicação em 24 h ao canal `seguranca@` (TCS, 15.2) |

---

## 13. Critérios de aceite

1. `/seja-colaborador` envia candidatura completa e exibe protocolo `EN430-COL-AAAA-NNNN`.
2. Nenhuma resposta, log ou HTML da rota pública contém CPF, RG ou endereço.
3. E-mail duplicado ativo é recusado com mensagem clara; candidatura arquivada permite novo envio.
4. Perfil clínico sem registro profissional não é aceito.
5. Honeypot preenchido ou envio em menos de 3 s é descartado sem gravar.
6. Link privado expirado é recusado com instrução de contato; token válido grava os dados uma única vez.
7. Geração do termo preenche **todos** os campos de 1.2 e o Anexo I, com hash registrado.
8. Numeração sequencial sem lacunas por ano, única no banco.
9. Painel mostra lista, ficha e histórico; toda transição aparece em `admin_log` com autor e motivo.
10. Painel de conformidade calcula os indicadores da seção 10.3 a partir do banco (sem número fixo).
11. Registro de emissão em `docs/juridico/README.md` não contém dado pessoal.
12. Acessos não podem ser marcados como concedidos sem termo assinado.
13. Testes PHPUnit cobrem: validação dos campos, duplicidade, perfil clínico, honeypot, token expirado,
    geração do termo, transições de estado e bloqueio de ativação sem assinatura.

---

## 14. Roadmap

| Fase | Entrega | Depende de | Estimativa |
|:--:|---|---|---|
| **F0** | Decisões D1–D6 e textos finais aprovados | outorgante | 0,5 dia |
| **F1** | Tabelas + rota pública + validações + confirmação + lista/ficha no admin + testes | F0 | 2–3 dias |
| **F2** | Link privado com token + geração do termo preenchido + numeração + hash | F1 | 2 dias |
| **F3** | Registro de assinatura + ativação/revogação de acesso + painel de conformidade + exportação | F2 | 2 dias |
| **F4** | Contas administrativas individuais com perfis e 2FA + e-mail transacional + conferência de registro profissional | F3, aliases de e-mail | 2–3 dias |

Entrega mínima útil: **F1 + F2** — já elimina a digitação manual do termo e cria o registro auditável.

---

## 15. Riscos e mitigação

| ID | Risco | Impacto | Mitigação |
|:--:|---|---|---|
| R1 | Coleta excessiva na página pública | alto (LGPD) | CPF/RG/endereço só na etapa privada autenticada |
| R2 | Senha administrativa única protege dados qualificatórios | alto | F4: contas individuais, hash bcrypt, 2FA; até lá, acesso restrito ao mantenedor |
| R3 | Spam e candidaturas falsas | médio | honeypot + tempo mínimo + limite por IP + triagem humana |
| R4 | Registro profissional declarado e não verdadeiro | médio (ético) | declaração no formulário, conferência manual e amostral, cláusula 9.2 do TCS |
| R5 | Termo assinado vazar por estar em pasta compartilhada | alto | armazenamento privado, acesso nominal, hash no banco |
| R6 | Candidato acreditar que o envio garante vaga/remuneração | médio | textos explícitos na página e na confirmação |
| R7 | Aliases de e-mail ainda inexistentes | médio | fluxo funciona sem e-mail; F4 depende da criação dos aliases |
| R8 | Dados de candidato ficarem órfãos após recusa | médio | rotina de expurgo em 12 meses, registrada em `admin_log` |

---

## 16. Decisões pendentes do outorgante

| ID | Decisão | Opções | Recomendação |
|:--:|---|---|---|
| D1 | Coletar CPF na página pública? | sim / não | **não** (etapa privada) |
| D2 | Quem aprova candidaturas? | mantenedor / comissão | mantenedor nesta fase |
| D3 | Remuneração de colaboradores | voluntária / mista | declarar "voluntária" como padrão e abrir exceção no Anexo I |
| D4 | Prazo de resposta ao candidato | 5 / 10 / 15 dias úteis | 10 dias úteis |
| D5 | Forma de assinatura | sistema / gov.br / duas vias | sistema + duas vias quando solicitado |
| D6 | Guarda de candidaturas recusadas | 6 / 12 / 24 meses | 12 meses |

---

## 17. Anexo A — Mapa campo → cláusula do termo

| Campo do formulário | TCS | Adesão |
|---|---|---|
| nome, nacionalidade, estado civil | 1.2 | cabeçalho |
| profissão / registro profissional | 1.2 | 2.1 (habilitação) |
| RG / CPF / endereço | 1.2, 21 | cabeçalho |
| e-mail / telefone | 1.2, 15.1 | 12 (comunicações) |
| função no projeto | 1.2 | Anexo I |
| áreas de interesse + módulos | Anexo I | Anexo I (matéria designada) |
| nível de acesso + sistemas | Anexo I | 5 (acessos) |
| acessa dados pessoais | Anexo I, 7.1 | 6 (dados de estudantes) |
| remuneração | 8.4, Anexo I | 10 (contraprestação) |
| conflito de interesses | 11.1 | 8 (impedimentos) |
| data de início | 1.2, Anexo I | 3 (vigência) |
| consentimento LGPD da candidatura | — | — (Política de Privacidade, item 12) |

---

## 18. Anexo B — Registro de controle (sem dados pessoais)

Modelo de linha a acrescentar ao [registro de emissão](../juridico/README.md#registro-de-emissão):

| Nº | Iniciais | Termo | Função / frente | Versão | Emissão | Situação |
|:--:|:--:|:-----:|-----------------|:------:|---------|----------|
| EN430-COL-2026-0001 | H. S. O. | TCS | mantenedor | 1.0 | 2026-11-22 | 🟦 assinado e ativo |

> O nome completo, CPF e endereço ficam apenas no exemplar assinado arquivado em local privado e no
> banco de produção — nunca neste repositório público (LGPD, art. 46).

---

*Planejamento v1.0 · Projeto EN_430 · 22/11/2026 · sujeito a aprovação do outorgante e à revisão
jurídica antes da implementação.*
