<?php

namespace App\Interfaces;

interface ItemPedidoGatewayInterface
{
    public function criar(array $itensPedido, int $idPedido): mixed;
    public function obterTodosItensPorPedido(int $idPedido): mixed;
}