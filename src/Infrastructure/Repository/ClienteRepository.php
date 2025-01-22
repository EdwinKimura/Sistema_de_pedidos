<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Cliente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class ClienteRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cliente::class);
        $this->em = $this->getEntityManager();
    }

    public function save(Cliente $cliente): Cliente
    {
        $this->em->persist($cliente);
        $this->em->flush();

        return $cliente;
    }

    public function findByCpf(string $cpf): ?Cliente
    {
        return $this->em->getRepository(Cliente::class)->findOneBy(['cpf' => $cpf]);
    }

    public function list(): array
    {
        return $this->em->getRepository(Cliente::class)->findAll();
    }
}