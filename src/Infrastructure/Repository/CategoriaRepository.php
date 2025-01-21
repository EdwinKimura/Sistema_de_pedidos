<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Categoria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class CategoriaRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categoria::class);
        $this->em = $this->getEntityManager();
    }

    public function save(Categoria $categoria): ?Categoria
    {
        $this->em->persist($categoria);
        $this->em->flush();

        return $categoria;
    }

    public function list(): array
    {
        return $this->em->getRepository(Categoria::class)->findAll();
    }
}
