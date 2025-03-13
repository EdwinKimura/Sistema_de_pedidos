<?php

namespace App\Interfaces;

use App\Domain\Entity\Pedido;

interface PedidoGatewayInterface
{
    public function criarPedido(Pedido $pedido): mixed;
    public function obterPedidoPorId(int $id): mixed;
    public function pagarPedido(Pedido $pedido): mixed;
    public function atualizarStatus(Pedido $pedido): mixed;
    public function detalhesPedido(int $pedidoId): mixed;
}