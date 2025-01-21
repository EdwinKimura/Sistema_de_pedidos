# Sistema de Pedidos - Lanchonete Fast-Food

## Descrição do Projeto
Este é um sistema de pedidos para uma lanchonete baseada em fast-food. Ele foi desenvolvido utilizando **Symfony** e **PostgreSQL**, seguindo a arquitetura hexagonal (ports & adapters) para garantir flexibilidade e manutenibilidade.

### Principais Funcionalidades
- **Cadastro de Clientes**: Permite cadastrar clientes, inclusive como objetos de valor.
- **Gerenciamento de Produtos**: Inserir, listar, atualizar e remover produtos, categorizados como "Lanche", "Bebida", "Acompanhamento" e "Sobremesa".
- **Gestão de Pedidos**: Criar e acompanhar pedidos com integração de filas para atualização de status em tempo real.
- **Painel Administrativo**: Gerenciamento completo de clientes, produtos e acompanhamento dos pedidos.
- **Documentação API**: Integrada com Swagger para facilitar a exploração e teste das APIs disponíveis.

## Tecnologias Utilizadas
- **Symfony** (Framework PHP)
- **Docker** e **Docker Compose**
- **OpenAPI/Swagger** (Documentação)

## Requisitos
- Docker (versão 20.x ou superior)
- Docker Compose (versão 2.x ou superior)
- Composer

## Como Configurar o Ambiente Localmente
Siga as instruções abaixo para configurar e executar o projeto localmente:

### 1. Clone o Repositório
```bash
git clone <url-do-repositorio>
cd sistema-pedidos
```

### 2. Configure as Variáveis de Ambiente
Crie um arquivo ou edite o `.env` e configure os seguintes valores:
```env
APP_ENV=dev
APP_SECRET=<sua-app-secret>
DATABASE_URL="postgresql://symfony_user:senha_segura@127.0.0.1:5432/sistema_pedidos"
```

### 3. Configurando o Dockerfile e docker-compose
Caso tenha alterado a variável DATABASE_URL do arquivo `.env` devemos trocar tb no Dockerfile e docker-compose

### 4. Suba os Contêineres Docker
Utilize o Docker Compose para construir e iniciar os contêineres:
```bash
docker-compose up -d --build
```
Isso iniciará:
- Um contêiner para a aplicação Symfony.
- Um contêiner para o PostgreSQL.

### 5. Acesse a Aplicação
- **API**: [http://localhost:8080](http://localhost:8080)
- **Swagger**: [http://localhost:8080/api/doc](http://localhost:8080/api/doc)

## Estrutura de Diretórios
- `src/Domain/Entity`: Contém as entidades e serviços principais.
- `src/Application/Service`: Casos de uso específicos da aplicação.
- `src/Infrastructure/Repository`: Adaptadores como os Repositórios.
