<?php

namespace App\Application\Service;

use App\Domain\Entity\Pedido;
use App\Domain\Entity\ItemPedido;
use App\Domain\Entity\Cliente;
use App\Infrastructure\Repository\PedidoRepository;
use App\Infrastructure\Repository\ItemPedidoRepository;
use App\Infrastructure\Repository\ProdutoRepository;

date_default_timezone_set('America/Sao_Paulo');
class PedidoService
{
    private PedidoRepository $repository;
    private ItemPedidoRepository $itemPedidoRepository;
    private ProdutoRepository $produtoRepository;

    public function __construct(
        PedidoRepository $repository,
        ItemPedidoRepository $itemPedidoRepository,
        ProdutoRepository $produtoRepository
    )
    {
        $this->repository = $repository;
        $this->itemPedidoRepository = $itemPedidoRepository;
        $this->produtoRepository = $produtoRepository;
    }

    public function save(?Cliente $cliente = null, array $itens): Pedido | \stdClass
    {
        $pedido = new Pedido();
        $pedido->setCliente($cliente);
        $pedido->setStatus('Recebido');
        $pedido->setValorTotal(0);
        $pedido->setCriadoEm(strtotime('now'));
        $pedido->setModificadoEm(strtotime('now'));

        $valorTotal = 0;
        $produtos = [];
        foreach ($itens as $item) {
            $produto = $this->produtoRepository->find($item['produtoId']);
            if(!$produto){
                $error = new \stdClass();
                $error->error = true;
                $error->code = 1;
                $error->message = 'Produto de ID '. $item['produtoId'] .' não encontrado';
                return $error;
            }
            $produtos[] = ["produto" => $produto, "quantidade" => $item['quantidade']];
        }

        foreach ($produtos as $produto) {
            $itemPedido = new ItemPedido();
            $itemPedido->setPedido($pedido);
            $itemPedido->setProduto($produto['produto']);
            $itemPedido->setQuantidade($produto['quantidade']);
            $valorTotal += $produto['produto']->getPreco() * $produto['quantidade'];
            $this->itemPedidoRepository->save($itemPedido);
        }

        $pedido->setValorTotal($valorTotal);
        return $this->repository->save($pedido);
    }

    public function atualizarStatus(int $pedidoId, string $status): Pedido
    {
        $pedido = $this->repository->find($pedidoId);
        if (!$pedido) {
            throw new \Exception('Pedido não encontrado');
        }
        $pedido->setStatus($status);
        $pedido->setModificadoEm(strtotime('now'));
        return $this->repository->save($pedido);
    }

    public function find(int $pedidoId): ?Pedido
    {
        return $this->repository->find($pedidoId);
    }

    public function listarPedidos(): array
    {
        return $this->repository->list();
    }

    public function listarPedidosPendentes(): array
    {
        return $this->repository->listByStatus();
    }
}
