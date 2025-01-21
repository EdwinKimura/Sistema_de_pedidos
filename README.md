# Sistema de Pedidos - Lanchonete Fast-Food

## Descrição do Projeto
Este é um sistema de pedidos para uma lanchonete baseada em fast-food. Ele foi desenvolvido utilizando **Symfony** e **MongoDB**, seguindo a arquitetura hexagonal (ports & adapters) para garantir flexibilidade e manutenibilidade. O sistema é integrado com o **Symfony Messenger** e **RabbitMQ** para o gerenciamento de filas, especialmente para o acompanhamento do status dos pedidos. 

### Principais Funcionalidades
- **Cadastro de Clientes**: Permite cadastrar clientes, inclusive como objetos de valor.
- **Gerenciamento de Produtos**: Inserir, listar, atualizar e remover produtos, categorizados como "Lanche", "Bebida", "Acompanhamento" e "Sobremesa".
- **Gestão de Pedidos**: Criar e acompanhar pedidos com integração de filas para atualização de status em tempo real.
- **Painel Administrativo**: Gerenciamento completo de clientes, produtos e acompanhamento dos pedidos.
- **Documentação API**: Integrada com Swagger para facilitar a exploração e teste das APIs disponíveis.

## Tecnologias Utilizadas
- **Symfony** (Framework PHP)
- **MongoDB** (Banco de dados NoSQL)
- **Docker** e **Docker Compose**
- **RabbitMQ** (Gerenciador de filas)
- **Symfony Messenger**
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
Crie um arquivo `.env.local` baseado no arquivo `.env` e configure os seguintes valores:
```env
APP_ENV=dev
APP_SECRET=<sua-app-secret>
MONGODB_URI=mongodb://mongo:27017
MESSENGER_TRANSPORT_DSN=amqp://guest:guest@rabbitmq:5672/%2f
```

### 3. Suba os Contêineres Docker
Utilize o Docker Compose para construir e iniciar os contêineres:
```bash
docker-compose up -d --build
```
Isso iniciará:
- Um contêiner para a aplicação Symfony.
- Um contêiner para o MongoDB.
- Um contêiner para o RabbitMQ.

### 4. Execute as Migrações e Seeders
A aplicação utiliza o MongoDB, mas algumas inicializações são necessárias:
```bash
docker exec -it php-container php bin/console doctrine:mongodb:schema:create

docker exec -it php-container php bin/console app:seed-produtos
```

### 5. Acesse a Aplicação
- **API**: [http://localhost:8000](http://localhost:8000)
- **Swagger**: [http://localhost:8000/api/doc](http://localhost:8000/api/doc)

## Testes Locais
O projeto utiliza **PHPUnit** para testes automatizados. Para executar os testes:
```bash
docker exec -it php-container php bin/phpunit
```

## Estrutura de Diretórios
- `src/Domain`: Contém as entidades e serviços principais.
- `src/Application`: Casos de uso específicos da aplicação.
- `src/Infrastructure`: Adaptadores, como Repositórios e integração com MongoDB e RabbitMQ.
- `src/UI`: Controladores e endpoints da API.

## Contribuindo
1. Faça um fork do projeto.
2. Crie uma nova branch para sua feature ou correção.
3. Faça um commit das suas alterações.
4. Envie suas alterações através de um pull request.

## Licença
Este projeto está licenciado sob a licença MIT. Veja o arquivo `LICENSE` para mais informações.
