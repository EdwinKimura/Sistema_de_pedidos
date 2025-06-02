<?php

namespace App\Interfaces;

use App\Domain\Entity\Cliente;

interface ClienteGatewayInterface
{

    public function obterTodosOsClientes(): array|null;
    public function obterClientePorId(int $id): array|null;
    public function obterClientePorCpf(string $cpf): array|null;
    public function obterClientePorCpfViaLambda(string $cpf): array|null;
    public function cadastrarCliente(Cliente $cliente): mixed;
}