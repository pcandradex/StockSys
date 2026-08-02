# Sistema de Gerenciamento de Estoque
# Projeto Acadêmico - UNINTER

Sistema web simples para gerenciamento de estoque, desenvolvido em PHP + MySQL, com controle de login, cadastro de produtos, ajustes de estoque (entrada/saída/ajuste), histórico de movimentações e relatórios com opção de impressão.

## Tecnologias

- PHP 8+
- MySQL (via mysqli)
- HTML / CSS puro (sem frameworks)

## Funcionalidades

- **Login** com senha protegida por hash (`password_hash`)
- **Cadastro de produtos** (SKU, descrição, preço, saldo, imagem)
- **Ajuste de estoque**: entrada, saída ou ajuste manual, sempre com motivo registrado
- **Histórico de movimentações**: consulta filtrável por produto, tipo e período
- **Relatório de estoque**: resumo em tela com opção de impressão

## Estrutura do projeto

```
├── includes/
│   └── menu.php          # menu de navegação compartilhado entre as páginas internas
├── conn.example.php       # modelo de configuração do banco (copiar para conn.php)
├── migracao.sql           # cria a tabela movimentacoes_estoque
├── ajustar_estoque.php
├── cadastrar.php          # cadastro de usuário do sistema
├── cadastros.php          # cadastro de produtos
├── editar_produto.php
├── get_produto.php
├── historico.php
├── index.php              # tela de login
├── logout.php
├── painel.php
├── processar_cadastro.php
├── produtos.php
├── protect.php            # protege páginas que exigem login
├── relatorio.php
└── styles.css
```

## Como configurar do zero

### 1. Banco de dados

Crie as tabelas na ordem abaixo (via phpMyAdmin ou similar):

```sql
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    imagem VARCHAR(255) DEFAULT NULL,
    preco DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    saldo INT NOT NULL DEFAULT 0,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

Depois, rode o `migracao.sql` deste repositório para criar a tabela de histórico (`movimentacoes_estoque`).

### 2. Configuração de conexão

Copie o arquivo de exemplo e preencha com suas credenciais reais:

```bash
cp conn.example.php conn.php
```

Edite `conn.php` com o host, usuário, senha e nome do banco. **Esse arquivo nunca deve ser commitado** (já está no `.gitignore`).

### 3. Primeiro usuário

Ainda não existe nenhum usuário cadastrado. Acesse `cadastrar.php` diretamente pela URL para criar o primeiro (esse link não aparece na tela de login por padrão, por segurança).

### 4. Pasta de uploads

O sistema salva as imagens de produtos em `uploads/`. Garanta que essa pasta exista e tenha permissão de escrita no servidor (o próprio código cria a pasta automaticamente na primeira vez, se possível).

## Requisitos do servidor

- PHP 8.0 ou superior
- Extensão `mysqli` habilitada
- MySQL/MariaDB

## Segurança

- Senhas de usuário são armazenadas com hash (`password_hash` / `password_verify`), nunca em texto puro.
- Consultas ao banco usam *prepared statements* ou escapam os dados (`real_escape_string`) para evitar SQL Injection.
- Uploads de imagem são validados por extensão, tipo MIME e tamanho máximo (5MB).
- **Nunca** commite o `conn.php` real — ele contém credenciais do banco de dados.
