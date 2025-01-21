<?php 

namespace App\Application\Service;

use App\Domain\Entity\Cliente;
use App\Infrastructure\Repository\ClienteRepository;

class ClienteService
{
    public function __construct(private ClienteRepository $repository)
    {
    }

    public function save(string $cpf = null, string $nome = null, string $email = null): Cliente
    {
        if($nome != null && $email != null)
        {
            $cliente = new Cliente($nome, $email);
            return $this->repository->save($cliente);
        }
        else
        {
            return new Cliente();
        }
    }

    public function findByEmail(string $email): Cliente
    {
        return $this->repository->findByEmail($email);
    }

    public function list(): array
    {
        return $this->repository->list();
    }
}