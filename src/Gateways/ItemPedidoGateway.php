<?php

namespace App\Gateways;

use App\Domain\Entity\ItemPedido;
use App\Interfaces\DbConnection;
use App\Interfaces\ItemPedidoGatewayInterface;

class ItemPedidoGateway implements ItemPedidoGatewayInterface
{
    private DbConnection $_db_connection;
    private string $table = 'item_pedidos';

    public function __construct(DbConnection $_db_connection){
        $this->_db_connection = $_db_connection;
    }

    public function criar(array $itensPedido, int $idPedido): mixed
    {
        foreach ($itensPedido as $item)
        {
            $itemCriado = $this->_db_connection->inserir(
                $this->table,
                [
                    'pedido_id' => $idPedido,
                    'produto_id' => $item['produto_id'],
                    'quantidade' => $item['quantidade']
                ]
            );

            if ($itemCriado === 0) return null;
        }

        return true;
    }

    public function obterTodosItensPorPedido(int $idPedido): mixed
    {
        $pedidos = $this->_db_connection->listarPorParametros($this->table, ['pedido_id' => $idPedido]);
        return $pedidos;
    }
}