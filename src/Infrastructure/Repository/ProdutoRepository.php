<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Produto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class ProdutoRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produto::class);
        $this->em = $this->getEntityManager();
    }

    public function save(Produto $produto): Produto
    {
        $this->em->persist($produto);
        $this->em->flush();

        return $produto;
    }

    public function findById(int $id): ?Produto
    {
        return $this->em->getRepository(Produto::class)->find($id);
    }

    public function list(): array
    {
        return $this->em->getRepository(Produto::class)->findAll();
    }

    public function delete(Produto $produto): void
    {
        $this->em->remove($produto);
        $this->em->flush();
    }
}