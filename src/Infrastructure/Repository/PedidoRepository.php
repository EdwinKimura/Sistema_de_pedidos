<?php 

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Pedido;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class PedidoRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pedido::class);
        $this->em = $this->getEntityManager();
    }

    public function save(Pedido $pedido): Pedido
    {
        $this->em->persist($pedido);
        $this->em->flush();
        return $pedido;
    }

    public function findById(int $id): ?Pedido
    {
        return $this->em->getRepository(Pedido::class)->find($id);
    }

    public function list(): array
    {
        return $this->em->getRepository(Pedido::class)->findAll();
    }

    public function listByStatus(): array
    {
        $query = $this->em->createQuery(
            'SELECT p
            FROM App\Domain\Entity\Pedido p
            WHERE p.status IN (:preparacao, :pronto)
            ORDER BY p.modificadoEm DESC'
        )
            ->setParameter('preparacao', 'Em Preparação')
            ->setParameter('pronto', 'Pronto');

        return $query->getResult();
    }
}
