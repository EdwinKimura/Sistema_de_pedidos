<?php

namespace App\Controller;

use App\Application\UseCases\ItemPedidoUseCase;
use App\Gateways\ItemPedidoGateway;
use App\Gateways\PedidoGateway;
use App\Interfaces\DbConnection;

class ItemPedidoController
{
    public static function criar(DbConnection $db_connection, int $idPedido, array $itens): mixed
    {
        $pedidoGateway = new PedidoGateway($db_connection);
        $itemPedidoGateway = new ItemPedidoGateway($db_connection);
        $itemPedidoUseCase = new ItemPedidoUseCase();

        $pedido = $itemPedidoUseCase->criar($pedidoGateway, $idPedido);

        if ($pedido === null)
        {
            return null;
        }

        return $itemPedidoGateway->criar($itens, $pedido->getId());
    }
}