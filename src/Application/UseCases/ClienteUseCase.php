<?php

namespace App\Application\UseCases;

use App\Domain\Entity\Cliente;
use App\Interfaces\ClienteGatewayInterface;

class ClienteUseCase
{
    public function obterTodosOsClientes(ClienteGatewayInterface $clienteGateway): array
    {
        return $clienteGateway->obterTodosOsClientes();
    }

    public function obterClientePorCpf(ClienteGatewayInterface $clienteGateway, string $cpf): array
    {
        return $clienteGateway->obterClientePorCpf($cpf);
    }

    public function cadastrarCliente(ClienteGatewayInterface $clienteGateway, array $cliente): Cliente|null
    {
        $busca = $clienteGateway->obterClientePorCpf($cliente['cpf']);

        if($busca) return null;

        return Cliente::fromArray($cliente);
    }

}