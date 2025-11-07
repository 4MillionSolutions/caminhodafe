# 📋 Requisitos do Cliente - Módulo de Agendamentos (ATUALIZADO)

## 🎯 Visão Geral
O módulo de agendamentos é a tela principal da Projearte. Precisa ser limpo, intuitivo e totalmente rastreável. As meninas (equipe) terão acesso principal. Sistema de cards/lista com status visual.

---

## 1️⃣ CADASTRO DO IMÓVEL (PRIMEIRO PASSO)

**Obs**: Deve ser cadastrado ANTES do agendamento. Não pode fazer agendamento sem imóvel.

### Campos Necessários:
```
├─ CEP (VIA CEP)
│  └─ Auto-preencher: Endereço, Bairro, Cidade, Estado
├─ ENDEREÇO (Auto-corriger maiúsculas/minúsculas)
├─ NÚMERO
├─ COMPLEMENTO
├─ BAIRRO (Auto-preenchido via CEP)
├─ CIDADE (Auto-preenchido via CEP)
├─ ESTADO (Auto-preenchido via CEP)
├─ TIPO (Select com opções pré-definidas)
│  ├─ CS (Casa)
│  ├─ AP (Apartamento)
│  ├─ LT (Lote)
│  ├─ GLP (GLP)
│  ├─ PD (Prédio)
│  ├─ LJ (Loja)
│  ├─ SL (Salão)
│  └─ OUTROS (Opção para cadastrar novo tipo)
└─ OBSERVAÇÕES (Interna - Equipe)
```

### Interface:
- ✅ Formulário limpo e intuitivo
- ✅ Validação de CEP via ViaCEP API
- ✅ Auto-preenchimento de campos
- ✅ Salvar imóvel
- ✅ Lista de imóveis cadastrados (cards ou tabela)
- ✅ Editar imóvel
- ✅ Deletar imóvel (só Master)

---

## 2️⃣ AGENDAMENTO (SEGUNDO PASSO)

### A. DADOS DO CLIENTE
```
├─ CLIENTE (Select - Puxar do Cadastro)
└─ IMÓVEL (Select - SOMENTE DOS IMÓVEIS JÁ CADASTRADOS)
```

### B. DADOS DA PROPOSTA
```
├─ OS INTERNA (Auto-gerada - Ex: AGD-001)
├─ OS PLATAFORMA (CLIENTE) (Input - informado pelo cliente)
├─ DATA DE CRIAÇÃO DA DEMANDA (CLIENTE) SLA
├─ DATA DE VENCIMENTO NA PLATAFORMA (CLIENTE) SLA
├─ SERVIÇOS (Multi-select - Puxar do Cadastro de Serviços)
│  └─ Ordenados por PRIORIDADES SLA
├─ OBSERVAÇÕES (Interno - Equipe)
└─ RASTREAMENTO
   ├─ Data/Hora de Criação
   └─ Quem Criou (User)
```

### C. DADOS DO AGENDAMENTO
```
├─ DATA AGENDADA (Date)
├─ HORA AGENDADA (Time)
├─ CONTATO (Nome - Quem receberá)
├─ NÚMERO DO CONTATO (Input - Format: (11) 97289-7338)
├─ PRESTADOR RECOMENDADO (NEW - CAMPO INTELIGENTE) ⭐
│  ├─ Busca AUTOMATICAMENTE prestadores por:
│  │  ├─ Estado do Imóvel
│  │  ├─ Cidade do Imóvel
│  │  └─ Bairro do Imóvel
│  ├─ Ordena por MENOR para MAIOR valor
│  ├─ Mostra LISTA com:
│  │  ├─ Nome do Prestador
│  │  ├─ Valor da Hora
│  │  ├─ Avaliação (Stars)
│  │  └─ Botão para selecionar
│  └─ Mostra ao lado do campo TÉCNICO RESPONSÁVEL
├─ TÉCNICO RESPONSÁVEL PRESTADOR (Select - Após selecionar recomendado)
├─ OBSERVAÇÃO EXTERNA (Para o Prestador - Ex: "Encontrar Moisés no lado X")
└─ RASTREAMENTO
   ├─ Data/Hora de Atribuição
   └─ Quem Atribuiu (User)
```

### D. DOCUMENTOS DA PROPOSTA
```
├─ MATRÍCULA (Upload - File)
├─ IPTU (Upload - File)
├─ MODELO DE LAUDO (Upload - Word/Excel/PDF)
├─ OUTROS (Upload - Múltiplos arquivos)
│  └─ Permitir até 5+ arquivos
│  └─ Ex: Cronogramas, Plantas, etc
└─ RASTREAMENTO
   ├─ Data/Hora do Upload
   └─ Quem Fez Upload (User)
```

---

## 3️⃣ FLUXO DO AGENDAMENTO

### Passo 1: Cadastro do Imóvel
```
1. Abrir formulário "Novo Imóvel"
2. Preencher CEP (ViaCEP auto-completa)
3. Confirmar/corrigir endereço, bairro, cidade, estado
4. Selecionar TIPO
5. Salvar Imóvel
6. Imóvel aparece na lista/cards
```

### Passo 2: Criar Agendamento
```
1. Clicar "Novo Agendamento"
2. Preencher dados do cliente
3. Selecionar IMÓVEL (somente cadastrados)
4. Preencher PROPOSTA (OS, SLA, Serviços)
5. Preencher AGENDAMENTO (Data, Hora, Contato)
6. Sistema mostra PRESTADORES RECOMENDADOS (automático)
7. Selecionar Prestador
8. Upload de Documentos
9. SALVAR
10. Sistema gera OSN INTERNA
11. Agendamento aparece em CARDS/LISTA
```

### Passo 3: Atribuição ao Prestador
```
OPÇÃO A: Automático ao Salvar
- Sistema envia automaticamente e-mail/WhatsApp

OPÇÃO B: Botão "Atribuir" nas Ações
- Menina clica "Atribuir"
- Sistema envia notificação
- Status muda para "Atribuído"
```

### Passo 4: Enviar para Produção
```
1. Status muda para "Em Produção"
2. Rastrear: Data/Hora, Quem enviou
3. Prestador já tem agendado
4. Não duplica horário no mesmo prestador
```

---

## 4️⃣ TELA PRINCIPAL (LISTA/CARDS)

### Deve Exibir:
```
┌─ FILTROS (Topo)
│  ├─ Por Cliente
│  ├─ Por Data
│  ├─ Por Prestador
│  ├─ Por Status
│  └─ Por Bairro/Cidade
├─ VISTA (Opção)
│  ├─ Cards (Visual)
│  └─ Tabela (Lista)
└─ CADA CARD/LINHA
   ├─ OS INTERNA
   ├─ Cliente
   ├─ Imóvel (Endereço)
   ├─ Data/Hora
   ├─ Prestador
   ├─ Status (Visual com cor)
   ├─ AÇÕES
   │  ├─ Editar
   │  ├─ Deletar (Só Master)
   │  ├─ Reagendar
   │  ├─ Retorno
   │  ├─ Reavaliação
   │  └─ Ver Detalhes
   └─ RASTREAMENTO (Ao clicar "Ver Detalhes")
      ├─ Histórico de mudanças
      ├─ Quem fez cada ação
      └─ Data/Hora de cada ação
```

### Status Possíveis:
```
🟡 RASCUNHO (Criado, não atribuído)
🟢 ATRIBUÍDO (Enviado ao prestador)
🔵 EM PRODUÇÃO (Produção recebeu)
✅ CONCLUÍDO (Trabalho feito, laudo enviado)
🔄 REAGENDADO (Reagendado por cliente)
🔁 RETORNO (Reativado para retorno)
📋 REAVALIAÇÃO (Nova avaliação do mesmo imóvel)
❌ CANCELADO (Cancelado)
```

---

## 5️⃣ AÇÕES ESPECIAIS

### A. REAGENDAR
```
1. Clicar "Reagendar" nas ações
2. Modal abre com dados preenchidos
3. Alterar: Data, Hora, Prestador
4. Salvar
5. Status = "REAGENDADO"
6. Notificar prestador (novo agendamento)
7. Rastrear: Quem reagendou, quando
```

### B. RETORNO
```
1. Clicar "Retorno" nas ações
2. Modalopen para confirmar
3. Gera NOVO número interno com "R" no início (Ex: R05482458)
4. Copia dados do agendamento anterior
5. Muda status para "RETORNO"
6. Envia para Produção
7. Prestador recebe nova demanda
8. Rastrear: Quem iniciou retorno, quando
```

### C. REAVALIAÇÃO
```
1. Clicar "Reavaliação" nas ações
2. Modal abre
3. Mostra agendamentos anteriores do MESMO IMÓVEL
4. Opção de COPIAR dados do anterior (opcional)
5. Permite editar serviços
6. Gera novo número interno
7. Mantém referência ao agendamento anterior
8. Rastrear: Quem criou reavaliação, quando
9. Ideal para demandas recorrentes (3 em 3 meses, consórcios, etc)
```

### D. DELETAR
```
- Somente usuário MASTER pode deletar
- Pedir confirmação
- Registrar no log: Quem deletou, quando, qual agendamento
```

---

## 6️⃣ RASTREAMENTO COMPLETO

**Todos os campos devem registrar:**
```
├─ Data/Hora da ação
├─ Usuário que fez
├─ Qual ação foi feita
├─ O quê foi alterado
└─ Campo para visualizar histórico completo
```

### Exemplo:
```
[2025-11-06 14:30:15] - Marcos Junior - CRIADO agendamento AGD-001
[2025-11-06 14:32:45] - Marcos Junior - ATRIBUÍDO ao prestador João Silva
[2025-11-06 15:00:00] - Sistema - ENVIADO para Produção
[2025-11-07 09:15:30] - Maria - REAGENDADO para 2025-11-08 10:00
[2025-11-07 09:16:00] - Sistema - NOTIFICADO prestador da alteração
```

---

## 7️⃣ NOTIFICAÇÕES AO PRESTADOR

Quando agendamento é criado/atribuído, enviar para:
```
├─ E-MAIL
│  ├─ Dados do agendamento
│  ├─ Endereço do imóvel
│  ├─ Data/Hora
│  ├─ Contato local
│  ├─ Observações externas
│  └─ Documentos anexados
├─ WHATSAPP
│  ├─ Mensagem resumida
│  ├─ Link para detalhes
│  └─ Confirmação de recebimento
└─ DASHBOARD DO PRESTADOR
   ├─ Nova demanda aparece
   └─ Pode confirmar recebimento
```

---

## 8️⃣ RECURSOS ADICIONAIS

### A. VERIFICAR PRODUÇÃO DO DIA
```
- Campo/Aba para VER PRODUÇÃO DO DIA
- Filtro por Prestador
- Mostra agendamentos do dia por prestador
- Impede duplicação de horário com mesmo prestador
- Mostra tempo entre agendamentos (para rota)
```

### B. IMPORTAÇÃO/EXPORTAÇÃO
```
├─ IMPORTAR
│  └─ CSV com agendamentos em lote
├─ EXPORTAR
│  ├─ Para Excel (Com filtros)
│  ├─ Para PDF (Relatório)
│  └─ Por período (Data inicial - final)
└─ RECORRÊNCIA
   ├─ Opção de criar agendamentos recorrentes
   └─ Ex: 3 em 3 meses, mensalmente, etc
```

### C. RELATÓRIOS
```
├─ Por Prestador (Produtividade)
├─ Por Cliente (Demandas)
├─ Por Período (Histórico)
├─ Por Status (Dashboard)
└─ De Faturamento (Valores dos serviços)
```

---

## 9️⃣ MUDANÇAS NO BANCO DE DADOS

### Tabela: agendamentos (ADICIONAR CAMPOS)
```sql
- os_interna (STRING UNIQUE)
- os_plataforma (STRING)
- data_criacao_demanda (DATETIME)
- data_vencimento_sla (DATETIME)
- contato_nome (STRING)
- numero_contato (STRING)
- observacao_externa (TEXT)
- data_criacao (DATETIME) - AUDIT
- usuario_criacao_id (FK)
- data_atribuicao (DATETIME) - AUDIT
- usuario_atribuicao_id (FK)
- data_producao (DATETIME) - AUDIT
- usuario_producao_id (FK)
- agendamento_referencia_id (FK) - Para Retorno/Reavaliação
- tipo_demanda (ENUM: ORIGINAL, RETORNO, REAVALIACAO)
- status (ENUM: RASCUNHO, ATRIBUIDO, PRODUCAO, CONCLUIDO, REAGENDADO, RETORNO, REAVALIACAO, CANCELADO)
```

### Tabela: agendamento_servicos (NOVA)
```sql
- id (PK)
- agendamento_id (FK)
- servico_id (FK)
- prioridade_sla (INT)
```

### Tabela: agendamento_auditoria (NOVA - Rastreamento)
```sql
- id (PK)
- agendamento_id (FK)
- usuario_id (FK)
- acao (STRING)
- campo_alterado (STRING)
- valor_anterior (TEXT)
- valor_novo (TEXT)
- data_acao (DATETIME)
```

### Tabela: prestadores (ATUALIZAR)
```sql
- ADICIONAR: estado_atendimento (STRING)
- ADICIONAR: cidade_atendimento (STRING)
- ADICIONAR: bairro_atendimento (STRING)
- ADICIONAR: valor_hora (DECIMAL)
- ADICIONAR: ativo (BOOLEAN)
```

---

## 🔟 PRIORIDADES DE IMPLEMENTAÇÃO

### FASE 1 (CRÍTICO):
```
1. ✅ Cadastro de Imóvel com ViaCEP
2. ✅ Agendamento básico com prestadores recomendados
3. ✅ Status e Rastreamento
4. ✅ Notificações (E-mail, WhatsApp)
5. ✅ Tela principal com Cards/Lista
```

### FASE 2 (IMPORTANTE):
```
1. Reagendar
2. Retorno (com novo número R)
3. Reavaliação
4. Verificar Produção do Dia
5. Relatórios básicos
```

### FASE 3 (NICE-TO-HAVE):
```
1. Importação/Exportação
2. Agendamentos Recorrentes
3. Dashboard de Prestadores
4. Gráficos e Analytics
```

---

## 📊 RESUMO DAS MUDANÇAS

| Funcionalidade | Status | Prioridade |
|---|---|---|
| Cadastro Imóvel com ViaCEP | ❌ NEW | CRÍTICA |
| Prestadores Recomendados (Inteligente) | ❌ NEW | CRÍTICA |
| Status e Rastreamento | ❌ NEW | CRÍTICA |
| Reagendar | ❌ NEW | ALTA |
| Retorno (Número com R) | ❌ NEW | ALTA |
| Reavaliação | ❌ NEW | ALTA |
| Notificações (Email/WhatsApp) | ❌ NEW | ALTA |
| Produção do Dia | ❌ NEW | MEDIA |
| Importação/Exportação | ❌ NEW | BAIXA |

---

## 🎨 DESIGN SUGERIDO

### Paleta de Cores por Status:
```
🟡 RASCUNHO: #FFC107 (Amarelo)
🟢 ATRIBUÍDO: #28A745 (Verde)
🔵 EM PRODUÇÃO: #007BFF (Azul)
✅ CONCLUÍDO: #6C757D (Cinza)
🔄 REAGENDADO: #E83E8C (Rosa)
🔁 RETORNO: #FD7E14 (Laranja)
📋 REAVALIAÇÃO: #17A2B8 (Ciano)
❌ CANCELADO: #DC3545 (Vermelho)
```

### Layout Sugerido:
```
┌─────────────────────────────────────────────┐
│ AGENDAMENTOS - Tela Principal               │
├─────────────────────────────────────────────┤
│ [Filtros] [Novo Agendamento] [Novo Imóvel] │
├─────────────────────────────────────────────┤
│                                             │
│ ┌──────────┐ ┌──────────┐ ┌──────────┐     │
│ │ AGD-001  │ │ AGD-002  │ │ AGD-003  │     │
│ │ Cliente  │ │ Cliente  │ │ Cliente  │     │
│ │ Imóvel   │ │ Imóvel   │ │ Imóvel   │     │
│ │ 14/11    │ │ 15/11    │ │ 15/11    │     │
│ │ João     │ │ Maria    │ │ Pedro    │     │
│ │ 🟢 Atrib │ │ 🔵 Prod  │ │ ✅ Conc  │     │
│ │ [E][D]   │ │ [E][D]   │ │ [E][D]   │     │
│ └──────────┘ └──────────┘ └──────────┘     │
│                                             │
└─────────────────────────────────────────────┘
```

---

## 📝 PRÓXIMOS PASSOS

1. **Criar Migration** para novos campos
2. **Criar Model** para Auditoria
3. **Criar Controller** com novas ações
4. **Criar Views** para novo Cadastro Imóvel
5. **Integrar ViaCEP**
6. **Implementar Prestadores Recomendados**
7. **Implementar Rastreamento**
8. **Implementar Notificações**
9. **Criar Tela de Cards/Lista**
10. **Testes E2E**

---

*Documento criado em: 6 de novembro de 2025*
*Próxima reunião: Validação com cliente*
