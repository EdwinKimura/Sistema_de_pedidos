<?php

namespace App\Controller;

use App\Application\UseCases\ClienteUseCase;
use App\Domain\Entity\Cliente;
use App\Gateways\ClienteGateway;
use App\Interfaces\DbConnection;

class ClienteController
{

    public static function listarTodosOsClientes(DbConnection $connection): array
    {
        $clienteGateway = new ClienteGateway($connection);
        $clienteUseCase = new ClienteUseCase();

        $clientes = $clienteUseCase->obterTodosOsClientes($clienteGateway);

        return $clientes;
    }

    public static function obterClientePorCpf(DbConnection $connection, string $cpf): array
    {
        $clienteGateway = new ClienteGateway($connection);
        $clienteUseCase = new ClienteUseCase();

        return $clienteUseCase->obterClientePorCpf($clienteGateway, $cpf);
    }

    public static function obterClientePorCpfViaLambda(DbConnection $connection, string $cpf): array
    {
        $clienteGateway = new ClienteGateway($connection);
        $clienteUseCase = new ClienteUseCase();

        return $clienteUseCase->obterClientePorCpfViaLambda($clienteGateway, $cpf);
    }

    public static function cadastrarCliente(DbConnection $connection, array $cliente): mixed
    {
        $clienteGateway = new ClienteGateway($connection);
        $clienteUseCase = new ClienteUseCase();

        $novoCliente = $clienteUseCase->cadastrarCliente($clienteGateway, $cliente);

        if($novoCliente === null)
        {
            return null;
        }

        return $clienteGateway->cadastrarCliente($novoCliente);
    }
}