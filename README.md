# Agencia de Viagens

Sistema web para gerenciamento de uma agencia de viagens. Permite cadastrar destinos, montar pacotes de viagem, registrar clientes e controlar reservas com gestao automatica de vagas.

## Tecnologias

- **PHP 8+** (sem frameworks)
- **MySQL 8** com PDO e prepared statements
- **HTML5 + CSS3** (variaveis CSS, grid, flexbox)
- **JavaScript** vanilla (auto-preenchimento de preco)

## Pre-requisitos

- PHP 8.0 ou superior
- MySQL 8.0 ou superior
- Servidor local (XAMPP, WAMP, Laragon ou similar)
- Navegador web moderno

## Como rodar

1. **Clone ou copie** o projeto para a pasta do servidor local:

   ```
   git clone <url-do-repositorio> TDE3baack
   ```

   Ou copie a pasta `TDE3baack` para `htdocs/` (XAMPP) ou `www/` (WAMP/Laragon).

2. **Importe o banco de dados.** Abra o phpMyAdmin ou terminal MySQL e execute:

   ```
   mysql -u root -p < database/schema.sql
   ```

   Isso cria o banco `agencia_viagens` com as tabelas e dados de exemplo.

3. **Configure a conexao.** Copie o arquivo de exemplo e preencha com suas credenciais:

   ```
   cp config/database.example.php config/database.php
   ```

   Edite `config/database.php` com seus dados:

   ```php
   $dbHost = 'localhost';
   $dbName = 'agencia_viagens';
   $dbUser = 'seu_usuario';
   $dbPass = 'sua_senha';
   ```

   > O arquivo `config/database.php` esta no `.gitignore` e nao sera versionado. O `database.example.php` serve como modelo.

4. **Acesse no navegador:**

   ```
   http://localhost/TDE3baack/
   ```

## Funcionalidades

### Dashboard
- Painel com contagens em tempo real (destinos, pacotes, clientes, reservas)
- Detalhamento de reservas pendentes e confirmadas
- Acoes rapidas para criar novos registros

### CRUD de Destinos
- Campos: nome, pais, descricao, clima
- Listagem, criacao, edicao e exclusao

### CRUD de Pacotes
- Campos: nome, destino (FK), duracao em dias, preco, vagas disponiveis
- Select de destino populado dinamicamente do banco

### CRUD de Clientes
- Campos: nome, email, telefone, CPF
- Validacao de CPF com algoritmo dos digitos verificadores
- Verificacao de unicidade de email e CPF com mensagem especifica

### CRUD de Reservas
- Campos: cliente (FK), pacote (FK), data da reserva, status, valor pago
- Selects de cliente e pacote populados do banco
- Auto-preenchimento do valor ao selecionar pacote (via JavaScript)
- Status restrito a: pendente, confirmada, cancelada (whitelist no backend)

### Controle de vagas
- Ao criar reserva: verifica vagas disponiveis; bloqueia se zeradas; decrementa em 1
- Ao cancelar reserva: devolve 1 vaga ao pacote (so se nao estava ja cancelada)
- Ao reativar reserva cancelada: verifica e consome vaga novamente
- Ao deletar reserva ativa: devolve 1 vaga ao pacote
- Todas as operacoes de vaga usam transactions PDO com `FOR UPDATE`

### Seguranca e validacao
- Todas as queries com prepared statements (PDO)
- Saida HTML escapada com `htmlspecialchars()`
- IDs validados com `filter_input(FILTER_VALIDATE_INT)`
- Validacao server-side em todos os formularios antes de qualquer query
- Erros inline por campo com destaque visual
- Mensagens de feedback via session (sucesso/erro)

## Estrutura de pastas

```
TDE3baack/
├── index.php              # Roteador principal (front controller)
├── .gitignore             # Arquivos ignorados pelo Git
├── config/
│   ├── app.php            # Nome, versao e constantes do sistema
│   ├── database.php       # Conexao PDO (ignorado pelo Git, suas credenciais)
│   └── database.example.php  # Modelo de conexao com placeholders
├── database/
│   └── schema.sql         # Script de criacao do banco e dados de exemplo
├── includes/
│   ├── header.php         # Abertura do HTML, meta tags, link do CSS
│   ├── nav.php            # Barra de navegacao com destaque da secao ativa
│   └── footer.php         # Rodape e fechamento do HTML
├── pages/
│   ├── dashboard.php      # Painel inicial com cards e contagens
│   ├── destinos/          # CRUD: index, criar, editar, deletar
│   ├── pacotes/           # CRUD: index, criar, editar, deletar
│   ├── clientes/          # CRUD: index, criar, editar, deletar
│   └── reservas/          # CRUD: index, criar, editar, deletar
└── assets/
    ├── css/
    │   └── style.css      # CSS completo (variaveis, layout, componentes)
    └── js/
        └── main.js        # JavaScript global
```

## Roteamento

O sistema usa um roteador simples via query string. Todas as paginas sao carregadas pelo `index.php`:

```
index.php                    → Dashboard
index.php?page=destinos      → Listagem de destinos
index.php?page=destinos_criar → Formulario de novo destino
index.php?page=destinos_editar&id=1 → Edicao do destino #1
```

O mesmo padrao se aplica para pacotes, clientes e reservas.
