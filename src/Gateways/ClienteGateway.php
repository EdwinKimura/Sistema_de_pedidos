<?php

namespace App\Gateways;

use App\Domain\Entity\Cliente;
use App\Interfaces\ClienteGatewayInterface;
use App\Interfaces\DbConnection;

class ClienteGateway implements ClienteGatewayInterface
{
    private DbConnection $connection;
    private string $table = 'clientes';

    public function __construct(DbConnection $connection){
        $this->connection = $connection;
    }

    public function obterTodosOsClientes(): array|null
    {
        return $this->connection->listarTodos($this->table);
    }

    public function obterClientePorCpf(string $cpf): array|null
    {
        return $this->connection->listarPorParametros($this->table, ['cpf' => $cpf]);
    }

    public function obterClientePorId(int $id): array|null
    {
        return $this->connection->listarPorParametros($this->table, ['id' => $id]);
    }

    public function cadastrarCliente(?Cliente $cliente): mixed
    {
        $valores = ['nome' => $cliente->getNome(), 'email' => $cliente->getEmail(), 'cpf' => $cliente->getCpf()];

        return $this->connection->inserir($this->table, $valores);
    }

    public function obterClientePorCpfViaLambda(string $cpf): array|null
    {
        return $this->connection->listarViaLambda($cpf);
    }
}