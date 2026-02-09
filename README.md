# Promoção Modelo

Sistema de campanha promocional desenvolvido com **Laravel 12**, **PostgreSQL** e **Bootstrap 5**.

Participantes cadastram cupons fiscais de compras e recebem números da sorte automaticamente para concorrer a prêmios.

---

## Requisitos

- PHP >= 8.2
- Composer
- PostgreSQL >= 14
- Node.js >= 18 e NPM (para assets com Vite)

### Extensões PHP necessárias

- `pdo_pgsql`
- `mbstring`
- `openssl`
- `tokenizer`
- `xml`
- `ctype`
- `json`
- `bcmath`
- `gd` (para exportação Excel)
- `zip` (para exportação Excel)

---

## Instalação

### 1. Clonar o repositório

```bash
git clone <url-do-repositorio> promocao-modelo
cd promocao-modelo
```

### 2. Instalar dependências

```bash
composer install
npm install
```

### 3. Configurar o ambiente

```bash
cp .env.example .env
php artisan key:generate
```

Edite o arquivo `.env` com as credenciais do seu PostgreSQL:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=promocao_modelo
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 4. Criar o banco de dados

```bash
createdb promocao_modelo
```

### 5. Executar migrations e seeds

```bash
php artisan migrate
php artisan db:seed
```

### 6. Compilar assets

```bash
npm run build
```

### 7. Iniciar o servidor

```bash
php artisan serve
```

Acesse: **http://localhost:8000**

### 8. Iniciar o Worker da Fila

O processamento dos cupons fiscais (geração de números da sorte) acontece de forma **assíncrona** através de uma fila dedicada. É obrigatório iniciar o worker para que os cupons sejam processados:

```bash
php artisan queue:work --queue=numeros-da-sorte --tries=1 --timeout=120
```

**Importante:**

- Use **apenas 1 worker** para esta fila, garantindo o processamento serializado (FIFO)
- O parâmetro `--tries=1` impede retentativas automáticas (erros bloqueiam a fila)
- Em produção, utilize o Supervisor para manter o worker ativo:

```ini
[program:numeros-da-sorte-worker]
process_name=%(program_name)s
command=php /caminho/do/projeto/artisan queue:work --queue=numeros-da-sorte --tries=1 --timeout=120 --sleep=3
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/caminho/do/projeto/storage/logs/worker.log
```

---

## Acessos de Teste

Após rodar o `db:seed`, os seguintes usuários estarão disponíveis:

### Administrador

| Campo | Valor |
|-------|-------|
| E-mail | `admin@promocaomodelo.com.br` |
| Senha | `admin123` |

O administrador padrão é um **Super Admin** com acesso total ao painel de controle em `/admin/login`, incluindo:

- **Dashboard** com gráficos de acompanhamento e estatísticas
- **Gerenciamento de produtos** participantes e bônus
- **FAQ** dinâmico
- **Configurações** da promoção (datas, valores, regras)
- **Controle da campanha** (encerrar/reabrir)
- **Gerenciamento de administradores** com permissões granulares

#### Permissões disponíveis

| Permissão | Descrição |
|-----------|-----------|
| `perm_produtos` | Cadastrar, editar e excluir produtos |
| `perm_faq` | Cadastrar, editar e excluir FAQs |
| `perm_configuracoes` | Alterar parâmetros da promoção |
| `perm_encerrar_campanha` | Encerrar ou reabrir a campanha |

Super Admins têm acesso irrestrito e podem gerenciar outros administradores. Tabela separada (`users`).

### Participante

| Campo | Valor |
|-------|-------|
| E-mail | `participante@example.com` |
| Senha | `senha123` |
| Nome | João da Silva |
| CPF | 111.222.333-44 |

O participante acessa via `/login`. Tabela separada (`participantes`).

---

## Executando os Testes

### 1. Criar o banco de testes

```bash
createdb promocao_modelo_test
```

### 2. Rodar os testes

```bash
php artisan test
```

Os testes cobrem as regras de negócio do `LuckyNumberService`, `CupomFiscalService` e `ProcessarCupomFiscal` (Job):

- Cálculo de números da sorte (R$ 20,00 = 1 número)
- Bônus proporcional: +1 número extra a cada R$ 20 em produtos bônus
- Geração aleatória sem repetição dentro de cada série
- Preenchimento sequencial de séries (0 a 9)
- Despacho assíncrono de jobs para processamento
- Processamento serializado (FIFO) dos cupons
- Bloqueio da fila em caso de erro
- Notificação de administradores em falhas
- Reprocessamento de cupons com erro

---

## Estrutura do Projeto

```
app/
├── Contracts/                    # Interfaces e DTOs
│   ├── InvoiceValidatorInterface.php
│   └── InvoiceValidationResult.php
├── Exports/                      # Exportações para Excel
│   ├── CuponsFiscaisExport.php
│   ├── NumerosDaSorteExport.php
│   └── ParticipantesExport.php
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── AdminCampanhaController.php        # Encerrar/reabrir campanha
│   │   │   ├── AdminCupomController.php           # Listagem e exportação de cupons
│   │   │   ├── AdminDashboardController.php       # Dashboard e gráficos
│   │   │   ├── AdminFaqController.php             # CRUD de FAQ
│   │   │   ├── AdminFilaController.php            # Fila de processamento
│   │   │   ├── AdminNotificacaoController.php     # Notificações do admin
│   │   │   ├── AdminNumeroDaSorteController.php   # Listagem e exportação de números
│   │   │   ├── AdminParticipanteController.php    # Listagem e exportação de participantes
│   │   │   ├── AdminProdutoController.php         # CRUD de produtos
│   │   │   └── AdminSettingController.php         # Configurações da promoção
│   │   ├── Auth/
│   │   │   ├── AdminLoginController.php   # Login admin (guard: admin)
│   │   │   ├── LoginController.php        # Login participante (guard: web)
│   │   │   └── RegisterController.php
│   │   ├── CupomFiscalController.php
│   │   ├── DashboardController.php
│   │   └── PageController.php
│   ├── Middleware/
│   │   └── VerificarPromocaoAtiva.php  # Bloqueia acesso quando campanha inativa
│   └── Requests/
│       ├── LoginRequest.php
│       ├── RegisterRequest.php
│       └── StoreCupomFiscalRequest.php
├── Jobs/
│   ├── Middleware/
│   │   └── EnsureFilaDesbloqueada.php  # Middleware que verifica bloqueio
│   └── ProcessarCupomFiscal.php        # Job de processamento assíncrono
├── Models/
│   ├── CupomFiscal.php
│   ├── Faq.php
│   ├── ItemCupom.php
│   ├── NumeroDaSorte.php
│   ├── Participante.php          # Tabela: participantes
│   ├── ProdutoParticipante.php
│   ├── Setting.php
│   └── User.php                  # Tabela: users (admin)
├── Notifications/
│   ├── CampanhaEncerrada.php       # Notificação de encerramento
│   └── ErroProcessamentoCupom.php  # Notificação de erro para admins
├── Providers/
│   └── AppServiceProvider.php
└── Services/
    ├── CupomFiscalService.php
    ├── LuckyNumberService.php
    ├── MockInvoiceValidator.php
    └── PromocaoService.php         # Controle de período e status da campanha
```

---

## Regras de Negócio

### Números da Sorte

- **R$ 20,00** em compras de produtos participantes = **1 número da sorte**
- **Produtos bônus** contam em dobro: além dos números base, o participante ganha **+1 número extra a cada R$ 20,00** do valor dos produtos bônus
- **10 séries** (0 a 9), cada uma com **10.000 números** (0000 a 9999)
- As séries são preenchidas **sequencialmente** (primeiro a série 0, depois a 1...)
- Dentro de cada série, os números são gerados de forma **aleatória e não repetida**
- Capacidade total: **100.000 números da sorte**

### Processamento Assíncrono

O processamento dos cupons fiscais (geração de números da sorte) é feito de forma **assíncrona** através do sistema de filas do Laravel:

1. **Serialização (FIFO):** Os cupons são processados na ordem exata de cadastro, garantindo que os números da sorte sejam distribuídos sequencialmente
2. **Fila dedicada:** Os jobs são despachados na fila `numeros-da-sorte`, separada das demais filas do sistema
3. **Worker único:** Apenas 1 worker deve processar esta fila, garantindo a serialização

#### Fluxo de Status do Cupom

```
Pendente → Validado (na fila) → Processando → Concluído
                                      ↓
                                     Erro → (Admin resolve) → Validado (reprocessa)
       → Rejeitado (Sefaz)
```

#### Tratamento de Erros

- Se um cupom **falha** durante o processamento, a fila é **bloqueada automaticamente**
- **Nenhum cupom subsequente** é processado enquanto o erro não for resolvido
- Os administradores são **notificados** por e-mail e pelo painel de controle
- O admin pode:
  - **Reprocessar** o cupom com erro (limpa números parciais e re-despacha)
  - **Desbloquear** a fila para retomar o processamento dos cupons pendentes

### Período e Encerramento da Campanha

A campanha respeita estritamente as datas e horários configurados:

- **Antes do início:** Cadastros e cupons são bloqueados. A landing page exibe contagem regressiva.
- **Durante o período:** A promoção aceita cadastros e cupons normalmente. Um countdown mostra o tempo restante.
- **Após o fim:** Cadastros e cupons são bloqueados automaticamente. Participantes podem fazer login para consultar seus números.
- **Esgotamento de números:** Se todos os 100.000 números forem distribuídos, a campanha é encerrada **imediatamente**, mesmo antes da data final.
- **Encerramento manual:** O admin pode encerrar a campanha a qualquer momento pelo painel.

**Proteção em tempo real:** Formulários de cadastro e cupom verificam o status da promoção via API a cada 30 segundos. Se a promoção encerrar enquanto o participante estiver preenchendo, os campos são desabilitados e uma mensagem é exibida. A submissão também é validada no servidor.

### Validação de Cupons

O sistema utiliza uma interface `InvoiceValidatorInterface` para validação de cupons junto à Sefaz. Atualmente um `MockInvoiceValidator` simula a consulta. Para integrar com a API real, basta criar uma nova classe que implemente a interface e registrar no `AppServiceProvider`.

---

## Configurações da Promoção

As regras da promoção são centralizadas na tabela `settings` e podem ser gerenciadas pelo painel administrativo:

| Chave | Valor Padrão | Descrição |
|-------|-------------|-----------|
| `valor_por_numero` | 20 | Valor em R$ para ganhar 1 número da sorte |
| `bonus_numeros` | proporcional | Números bônus proporcionais ao valor dos produtos bônus (R$ 20 = +1) |
| `data_inicio` | 2025-01-01 00:00:00 | Data e hora de início da promoção |
| `data_fim` | 2025-12-31 23:59:59 | Data e hora de término da promoção |
| `total_series` | 10 | Total de séries (0 a 9) |
| `numeros_por_serie` | 10000 | Números por série (0000 a 9999) |
| `fila_bloqueada` | false | Status da fila de processamento |
| `fila_cupom_erro_id` | - | ID do cupom que causou o bloqueio |
| `campanha_encerrada` | false | Encerramento manual/por esgotamento |
| `campanha_motivo_encerramento` | - | Motivo: esgotamento, manual |

---

## Relatórios e Exportação

O painel administrativo oferece listagens completas para acompanhamento e validação da promoção:

### Participantes (`/admin/participantes`)

- Lista todos os participantes cadastrados com paginação
- Busca por nome, CPF, e-mail ou cidade
- Exibe contagem de cupons e números da sorte por participante
- Visualização detalhada com dados pessoais, cupons e números
- Exportação para Excel (.xlsx)

### Cupons Fiscais (`/admin/cupons`)

- Lista todos os cupons com paginação e filtro por status
- Busca por número do cupom, nome ou CPF do participante
- Cards de resumo com contagem por status (Pendente, Na Fila, Processando, Concluído, Erro, Rejeitado)
- Visualização detalhada com itens do cupom, dados do participante e números gerados
- Exportação para Excel (.xlsx)

### Números da Sorte (`/admin/numeros-da-sorte`)

- Lista todos os números distribuídos com paginação
- Busca por nome, CPF do participante ou número
- Filtro por série (0 a 9) com contagem
- Barra de progresso da capacidade utilizada
- Links diretos para o participante e cupom relacionados
- Exportação para Excel (.xlsx)

> Os botões de exportação respeitam os filtros de busca aplicados, gerando planilhas apenas com os dados filtrados.

---

## Tecnologias

- **Backend:** Laravel 12 / PHP 8.2+
- **Banco de Dados:** PostgreSQL
- **Filas:** Laravel Queue (driver: database)
- **Frontend:** Blade + Bootstrap 5 + Bootstrap Icons
- **Gráficos:** Chart.js
- **Exportação:** Maatwebsite/Excel (PhpSpreadsheet)
- **Testes:** PHPUnit
