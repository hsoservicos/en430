# POLÍTICA DE PRIVACIDADE

**Sistema de Avaliação — Projeto EN_430**

| | |
|---|---|
| **Versão** | 1.0 |
| **Vigência** | 21 de novembro de 2026 |
| **Aplicação** | `https://en430.appservice.tec.br` (sistema de avaliação) e demais páginas do projeto sob esse domínio |
| **Publicação** | `https://en430.appservice.tec.br/politica-de-privacidade` (renderizada a partir deste documento) |
| **Controlador** | Humberto Santos de Oliveira — CPF 715.591.404-53 |
| **Encarregado (DPO)** | O próprio controlador — contato: privacidade@en430.appservice.tec.br |

> Documento elaborado em conformidade com a **Lei nº 13.709/2018 (LGPD)**, o **Marco Civil da
> Internet (Lei nº 12.965/2014)** e o **Código de Defesa do Consumidor**, quando aplicável.
> Sujeito a revisão jurídica antes da publicação em produção.

---

## 1. Quem trata seus dados

O **Sistema de Avaliação do Projeto EN_430** é uma ferramenta de **apoio ao estudo** de conteúdos de
Enfermagem. Ele não é instituição de ensino, não emite certificado e não substitui curso
regulamentado.

O **controlador** dos dados pessoais é **Humberto Santos de Oliveira**, pessoa física, com endereço
na Rua Amoirinópolis, 86 - A, Nossa Senhora Ó, Paulista - PE, CPF 715.591.404-53.

**Cenário institucional:** quando o sistema é utilizado por uma instituição de ensino, nos termos de
ajuste próprio, a **instituição é a controladora** dos dados de seus estudantes e o projeto atua como
**operador**, seguindo suas instruções. Nesse caso, pedidos de titular devem ser dirigidos
primeiramente à instituição.

---

## 2. A quem esta política se aplica

Aplica-se a **estudantes** que criam conta no sistema de avaliação e a visitantes das páginas
públicas do domínio. Não se aplica a sites de terceiros eventualmente acessados por links, que têm
políticas próprias.

---

## 3. Definições essenciais

| Termo | Significado |
|---|---|
| **Dado pessoal** | Informação sobre pessoa identificada ou identificável |
| **Dado sensível** | Dado sobre saúde, origem racial ou étnica, convicção religiosa, opinião política, filiação sindical, vida sexual, dado genético ou biométrico |
| **Titular** | Pessoa a quem os dados se referem — neste caso, o(a) estudante |
| **Tratamento** | Coleta, uso, armazenamento, compartilhamento, eliminação e demais operações com dados |
| **Controlador** | Quem decide sobre o tratamento — indicado no item 1 |
| **Operador** | Quem trata dados em nome do controlador |

---

## 4. Dados que tratamos

### 4.1. Dados informados por você no cadastro

| Dado | Obrigatório? | Observação |
|---|---|---|
| Nome completo | Sim | Identificação no painel e nos resultados |
| E-mail | Sim | Identificação de acesso; não é usado para marketing |
| Senha | Sim | Armazenada apenas como **hash bcrypt (custo 12)** — nunca em texto legível |
| Data de nascimento | Não | Usada para adequação etária, quando informada |
| Telefone | Não | Usado apenas na recuperação de acesso por nome + telefone |

### 4.2. Dados gerados pelo uso

| Dado | Finalidade |
|---|---|
| Avaliações realizadas: data de início e fim, identificadores das questões sorteadas, respostas registradas, pontuação, resultado e situação | Correção, histórico de desempenho e melhoria do banco de itens |
| Progresso por módulo | Exibição do seu desempenho no painel |
| Registros administrativos (`admin_log`): ação realizada, detalhes e data | Auditoria de operações sensíveis, como exclusão de conta, redefinição de senha e exportação de dados |

### 4.3. Dados técnicos

| Dado | Finalidade | Armazenamento |
|---|---|---|
| Cookie de sessão (`HttpOnly`, `SameSite=Lax`, `Secure` em HTTPS, validade de 24h) | Manter você autenticado | Sessão do servidor |
| Hash do endereço IP | Limitar tentativas de login (5 tentativas / bloqueio de 60s) | Apenas na sessão; não é gravado em banco |
| Registros de acesso do servidor e do proxy (data, hora, IP, navegador) | Segurança, prevenção a fraude e cumprimento do art. 15 do Marco Civil | Logs do provedor de hospedagem |

**Não coletamos** dados sensíveis de saúde no cadastro. O sistema não pede diagnóstico, prontuário ou
informação clínica de estudante.

---

## 5. Para que usamos seus dados e com qual base legal

| Finalidade | Dados | Base legal (LGPD) |
|---|---|---|
| Criar e manter sua conta; permitir login e recuperação de acesso | Nome, e-mail, senha, telefone | Consentimento — art. 7º, I |
| Registrar suas avaliações, corrigir e exibir seu desempenho | Respostas, pontuação, datas | Consentimento — art. 7º, I |
| Adequação etária e proteção de menores | Data de nascimento | Consentimento — art. 7º, I, e art. 14 |
| Segurança: prevenção a acesso indevido, fraude e abuso | Cookie de sessão, hash de IP, logs | Legítimo interesse — art. 7º, IX |
| Guarda de registros de acesso a aplicação de internet | Logs de acesso | Obrigação legal — art. 7º, II, e art. 15 do Marco Civil |
| Melhorar o banco de itens e a qualidade pedagógica, com dados agregados ou anonimizados | Estatísticas de acerto por item | Legítimo interesse — art. 7º, IX |

**Não utilizamos** seus dados para publicidade, perfilamento comercial, venda, decisões automatizadas
com efeito jurídico ou envio de newsletter.

---

## 6. Compartilhamento

**6.1.** **Não vendemos, alugamos nem cedemos** dados pessoais a terceiros para fins comerciais.

**6.2.** O compartilhamento ocorre apenas com:

| Destinatário | Motivo |
|---|---|
| Provedor de hospedagem e infraestrutura | Executar a aplicação e manter o banco de dados |
| Autoridades públicas | Cumprimento de ordem legal, judicial ou regulatória |
| Profissionais de segurança e jurídico contratados | Investigação de incidente ou exercício regular de direitos |

**6.3.** Os prestadores atuam como **operadores**, vinculados a dever de confidencialidade e à
finalidade específica.

**6.4.** O acesso interno é restrito ao mantenedor do projeto, por credencial individual, e as ações
sensíveis ficam registradas em log de auditoria.

---

## 7. Transferência internacional

A aplicação é hospedada em infraestrutura contratada pelo controlador. Caso o provedor mantenha dados
fora do Brasil, a transferência observará os arts. 33 a 36 da LGPD, com cláusulas contratuais e
garantia de nível de proteção adequado. A localização do servidor pode ser consultada pelo canal do
item 11.

---

## 8. Por quanto tempo guardamos

| Dado | Prazo |
|---|---|
| Dados de conta (nome, e-mail, telefone, data de nascimento) | Enquanto a conta existir |
| Avaliações e resultados | Enquanto a conta existir, para manter o histórico pedagógico |
| Após pedido de exclusão | Eliminação em até **30 (trinta) dias**, ressalvadas as hipóteses do item 8.1 |
| Registros de acesso (logs) | **6 (seis) meses**, nos termos do art. 15 do Marco Civil |
| Registros administrativos de auditoria | **5 (cinco) anos**, para comprovar operações e exercício regular de direitos |
| Cópias de segurança | Descartadas no ciclo de **30 (trinta) dias**, quando existirem |

**8.1.** A eliminação pode ser parcialmente limitada por obrigação legal, regulatória, exercício
regular de direitos ou pelos prazos acima, hipóteses em que o dado permanece bloqueado, sem uso, até
o término do prazo.

**8.2.** Contas inativas por mais de **24 (vinte e quatro) meses** podem ser eliminadas, com aviso
prévio ao e-mail cadastrado, quando tecnicamente possível.

---

## 9. Como protegemos os dados

| Medida | Situação |
|---|---|
| Senhas com hash **bcrypt (custo 12)** | Implementado |
| Comunicação por **HTTPS** | Implementado no ambiente de produção |
| Cookie de sessão `HttpOnly`, `SameSite=Lax` e `Secure` em HTTPS | Implementado |
| Proteção **CSRF** em formulários | Implementado |
| Consultas ao banco com **prepared statements** | Implementado |
| Limite de tentativas de login (5 tentativas / 60s de bloqueio) | Implementado |
| Painel administrativo com segredo próprio e log de ações | Implementado |
| Acesso administrativo restrito e credenciais individuais | Implementado |
| Banco de dados em volume persistente, fora do diretório público | Implementado |

**9.1.** Nenhum sistema é totalmente imune a incidentes. Em caso de incidente de segurança que possa
acarretar risco ou dano relevante, o controlador comunicará os titulares afetados e a **ANPD**, nos
termos dos arts. 48 e 49 da LGPD, com descrição do ocorrido, dados afetados e medidas adotadas.

---

## 10. Seus direitos

Você pode, a qualquer momento, solicitar:

| Direito | O que significa |
|---|---|
| **Confirmação e acesso** | Saber se tratamos seus dados e obter cópia |
| **Correção** | Corrigir dado incompleto, inexato ou desatualizado |
| **Anonimização, bloqueio ou eliminação** | De dados desnecessários, excessivos ou tratados em desconformidade |
| **Portabilidade** | Receber seus dados em formato estruturado (CSV ou JSON) |
| **Eliminação** | Excluir os dados tratados com base em consentimento |
| **Informação** | Saber com quem compartilhamos e as consequências da negativa de consentimento |
| **Revogação do consentimento** | A qualquer momento, sem custo |
| **Oposição** | Opor-se a tratamento fundado em legítimo interesse, com análise fundamentada |
| **Reclamação** | Peticionar à **ANPD** e aos órgãos de defesa do consumidor |

**10.1. Como exercer:** envie pedido para **privacidade@en430.appservice.tec.br**, informando nome e
e-mail cadastrados. Podemos pedir confirmação de identidade para evitar acesso indevido.

**10.2. Prazo:** resposta em até **15 (quinze) dias** contados do pedido, salvo hipótese de
complexidade justificada (art. 19, II, da LGPD).

**10.3.** A exclusão da conta remove seus dados de cadastro e suas avaliações, mantidos apenas os
registros de acesso e de auditoria pelos prazos legais do item 8. A exclusão é **irreversível**.

**10.4. Autoatendimento.** A exclusão pode ser feita pelo próprio titular em
`/excluir-conta`, mediante senha e confirmação textual. É registrada em log de auditoria e produz
efeito **imediato**, dentro do prazo de 30 dias do item 8. Pedidos por e-mail continuam aceitos para
quem não conseguir acessar a conta.

---

## 11. Contato e encarregado

| Canal | Uso |
|---|---|
| **privacidade@en430.appservice.tec.br** | Direitos do titular, dúvidas e reclamações sobre dados pessoais |
| **seguranca@en430.appservice.tec.br** | Relato de incidente de segurança, vazamento ou vulnerabilidade |

O **encarregado pelo tratamento de dados (DPO)** é o próprio controlador, Humberto Santos de
Oliveira, cujo contato consta no quadro do início deste documento.

Você também pode reclamar diretamente à **Autoridade Nacional de Proteção de Dados (ANPD)** —
gov.br/anpd.

---

## 12. Cookies

**12.1.** Usamos **apenas cookies essenciais**:

| Cookie | Finalidade | Duração |
|---|---|---|
| Cookie de sessão PHP (`PHPSESSID`) | Manter você autenticado e proteger formulários contra CSRF | 24 horas ou até o logout |

**12.2.** **Não usamos** cookies de publicidade, análise de audiência, rastreamento entre sites ou
redes sociais.

**12.3.** O bloqueio do cookie essencial impede o login, porque a autenticação depende dele.

**12.4.** O consentimento é coletado no cadastro por **checkbox obrigatório** e registrado no banco
com **data e hora** e **versão** desta política (`consentimento_em` e `consentimento_versao`).

---

## 13. Crianças e adolescentes

**13.1.** O sistema destina-se majoritariamente a estudantes de curso técnico, em geral maiores de 18
anos. Quando utilizado por pessoa **menor de 18 anos**, o tratamento observará o art. 14 da LGPD, no
**melhor interesse** do adolescente:

a) o cadastro de menor de 16 anos depende de consentimento e acompanhamento de responsável legal;
b) dados de menores não serão usados para finalidade diversa da educacional;
c) o responsável pode solicitar acesso, correção e eliminação pelo canal do item 11.

**13.2.** Não há coleta intencional de dados de crianças menores de 12 anos; constatada a coleta,
os dados serão eliminados.

---

## 14. Decisões automatizadas

**14.1.** A correção das avaliações é **automática** (comparação com o gabarito) e gera apenas
**pontuação pedagógica**, sem efeito jurídico, sem perfil comportamental e sem consequência
disciplinar.

**14.2.** Você pode solicitar **revisão humana** da correção pelo canal do item 11, com análise do
mantenedor.

---

## 15. Alterações desta política

**15.1.** Esta política pode ser atualizada por mudança legal, técnica ou de finalidade. A versão
vigente, com data, estará sempre publicada neste endereço.

**15.2.** Alteração relevante será comunicada no sistema ou por e-mail, com antecedência razoável,
quando houver canal disponível.

**15.3.** O uso do sistema após a vigência da nova versão implica ciência, sem prejuízo da
possibilidade de revogar o consentimento.

**15.4.** Quando a versão desta política mudar, o acesso ao painel e às avaliações fica suspenso
até o registro de novo aceite, com a nova versão identificada. O histórico do estudante é
preservado nesse intervalo e a exclusão da conta permanece disponível a qualquer momento.

---

## 16. Legislação e foro

Esta política é regida pelas leis brasileiras, em especial a **LGPD (Lei nº 13.709/2018)**, o
**Marco Civil da Internet (Lei nº 12.965/2014)** e o **Código Civil**. Fica eleito o foro da
**Comarca de Paulista, Estado de Pernambuco**, para controvérsias, sem prejuízo do direito do
consumidor de demandar em seu domicílio, quando aplicável.

---

## 17. Resumo em uma página

| Pergunta | Resposta |
|---|---|
| Preciso pagar? | Não |
| Vendem meus dados? | Não |
| Usam meus dados para propaganda? | Não |
| Quais dados são obrigatórios? | Nome, e-mail e senha |
| Posso apagar minha conta? | Sim, pelo canal privacidade@, com exclusão em até 30 dias |
| Meus dados de saúde são coletados? | Não |
| Há rastreamento entre sites? | Não |
| Como falo sobre privacidade? | privacidade@en430.appservice.tec.br |

---

*Política de Privacidade v1.0 · Projeto EN_430 · vigência 21/11/2026 · sujeita a revisão jurídica.*
