<?php

namespace App\Controller;

use App\Api\ItemPedidoApi;
use App\Application\UseCases\PedidoUseCase;
use App\Domain\Entity\ItemPedido;
use App\Domain\Entity\Pedido;
use App\Gateways\CategoriaGateway;
use App\Gateways\ClienteGateway;
use App\Gateways\ItemPedidoGateway;
use App\Gateways\PedidoGateway;
use App\Gateways\ProdutoGateway;
use App\Interfaces\DbConnection;
use Symfony\Component\HttpFoundation\JsonResponse;

class PedidoController
{
    public static function listarTodos(DbConnection $connection): array
    {
        $pedidoGateway = new PedidoGateway($connection);

        return $pedidoGateway->listarTodos();
    }

    public static function listarTodosPendentes(DbConnection $connection): array
    {
        $pedidoGateway = new PedidoGateway($connection);

        return $pedidoGateway->listarTodosPendentes();
    }

    public static function detalhesPedido(DbConnection $connection, int $pedidoId): mixed
    {
        $clienteGateway    = new ClienteGateway($connection);
        $produtoGateway    = new ProdutoGateway($connection);
        $itemPedidoGateway = new ItemPedidoGateway($connection);
        $categoriaGateway  = new CategoriaGateway($connection);
        $pedidoGateway     = new PedidoGateway($connection);
        $pedidoUseCase     = new PedidoUseCase();

        $pedido = $pedidoUseCase->detalhesPedido(
            pedidoGateway: $pedidoGateway,
            clienteGateway: $clienteGateway,
            produtoGateway: $produtoGateway,
            itemPedidoGateway: $itemPedidoGateway,
            categoriaGateway: $categoriaGateway,
            pedidoId: $pedidoId
        );

        if($pedido === null) return null;

        return $pedido;
    }

    public static function criarPedido(DbConnection $connection, array $pedido): mixed
    {
        $clienteGateway = new ClienteGateway($connection);
        $produtoGateway = new ProdutoGateway($connection);
        $pedidoGateway = new PedidoGateway($connection);
        $pedidoUseCase = new PedidoUseCase();

        $pedidoFormalizado = $pedidoUseCase->criarPedido($clienteGateway, $produtoGateway, $pedido);

        if($pedidoFormalizado === null) return -2;

        $idPedidoCriado = $pedidoGateway->criarPedido($pedidoFormalizado);

        if($idPedidoCriado === null) return -1;

        $itemPedido = ItemPedidoController::criar($connection, $idPedidoCriado, $pedidoFormalizado->getItens());

        if($itemPedido === null) return -3;

        return $itemPedido;
    }

    public static function pagarPedido(DbConnection $connection, float $valor, int $pedidoId): mixed
    {
        $pedidoGateway = new PedidoGateway($connection);
        $pedidoUseCase = new PedidoUseCase();

        $pedido = $pedidoUseCase->pagarPedido($pedidoGateway, $valor, $pedidoId);

        if($pedido === null) return null;

        if($pedido === -1) return -1;

        return $pedidoGateway->pagarPedido($pedido);
    }

    public static function atualizarPedido(DbConnection $connection, int $pedidoId, string $status): mixed
    {
        $pedidoGateway = new PedidoGateway($connection);
        $pedidoUseCase = new PedidoUseCase();

        $pedido = $pedidoUseCase->pedidoValido($pedidoGateway, $pedidoId, $status);

        if($pedido === null) return null;
        if($pedido === -1) return -1;

        return $pedidoGateway->atualizarStatus($pedido);
    }
}