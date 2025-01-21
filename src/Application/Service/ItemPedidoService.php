<?php

namespace App\Application\Service;

use App\Domain\Entity\ItemPedido;
use App\Infrastructure\Repository\ItemPedidoRepository;
use App\Infrastructure\Repository\PedidoRepository;
use App\Infrastructure\Repository\ProdutoRepository;

class ItemPedidoService
{
    private ItemPedidoRepository $repository;
    private PedidoRepository $pedidoRepository;
    private ProdutoRepository $produtoRepository;

    public function __construct(
        ItemPedidoRepository $repository,
        PedidoRepository $pedidoRepository,
        ProdutoRepository $produtoRepository
    )
    {
        $this->repository = $repository;
        $this->pedidoRepository = $pedidoRepository;
        $this->produtoRepository = $produtoRepository;
    }

    public function save($pedidoId, $produtoId, $quantidade): ItemPedido
    {
        $pedido = new ItemPedido();
        $pedido->setPedido($this->pedidoRepository->find($pedidoId));
        $pedido->setProduto($this->produtoRepository->find($produtoId));
        $pedido->setQuantidade($quantidade);
        return $this->repository->save($pedido);

    }

    public function find(int $pedidoId): array
    {
        return $this->repository->find($pedidoId);
    }

    public function listarItensPedido(int $pedidoId): array
    {
        return $this->repository->findAllByPedido($pedidoId);
    }
}
