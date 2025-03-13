<?php

namespace App\Application\UseCases;

use App\Domain\Entity\Pedido;
use App\Interfaces\PedidoGatewayInterface;

class ItemPedidoUseCase
{
    public function criar(PedidoGatewayInterface $pedidoGateway, int $idPedido): Pedido|null
    {
        $busca = $pedidoGateway->obterPedidoPorId($idPedido);

        if(empty($busca)) return null;

        $pedido = Pedido::fromArray($busca);

        return $pedido;
    }
}