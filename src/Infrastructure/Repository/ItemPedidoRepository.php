<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\ItemPedido;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class ItemPedidoRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $em;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemPedido::class);
        $this->em = $this->getEntityManager();
    }

    public function save(ItemPedido $itemPedido): ItemPedido
    {
        $this->em->persist($itemPedido);
        $this->em->flush();

        return $itemPedido;
    }

    public function findAllByPedido(int $pedidoId): array
    {
        $itensPedido = $this->findBy(['pedido' => $pedidoId]);
        return $itensPedido;
    }
}
