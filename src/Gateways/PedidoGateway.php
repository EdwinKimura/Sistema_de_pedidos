<?php

namespace App\Gateways;

use App\Domain\Entity\Pedido;
use App\Interfaces\DbConnection;
use App\Interfaces\PedidoGatewayInterface;

class PedidoGateway implements PedidoGatewayInterface
{
    private DbConnection $_db_connection;
    private string $table = 'pedidos';

    public function __construct(DbConnection $_db_connection){
        $this->_db_connection = $_db_connection;
    }

    public function listarTodos(): array
    {
        return $this->_db_connection->listarTodos($this->table);
    }

    public function listarTodosPendentes(): array
    {
        return $this->_db_connection->listarPorParametros($this->table, ['status' => 'Finalizado'], order: ['modificado_em' => 'ASC'], modoCondicao: ' != ');
    }

    public function criarPedido(Pedido $pedido): mixed
    {
        $pedidoCriado = $this->_db_connection->inserir(
            $this->table,
            [
                'cliente_id' => $pedido->getCliente(),
                'status' => $pedido->getStatus(),
                'valor_total' => $pedido->getValorTotal(),
                'criado_em' => $pedido->getCriadoEm(),
                'modificado_em' => $pedido->getModificadoEm()
            ],
            true
        );

        if ($pedidoCriado === 0) return null;

        return $pedidoCriado;
    }

    public function obterPedidoPorId(int $id): mixed
    {
        return $this->_db_connection->listarPorParametros($this->table, ['id' => $id])[0] ?? [];
    }

    public function detalhesPedido(int $pedidoId): mixed
    {
        $queryBuilder = $this->_db_connection->getConnection()->createQueryBuilder();
        $queryBuilder
            ->select(
                'c.cpf',
                'c.nome',
                'c.email'
            )
            ->from($this->table, 'p')
            ->innerJoin('p', 'clientes', 'c', 'c.id = p.cliente_id')
            ->innerJoin('p', 'item_pedidos', 'ip', 'ip.pedido_id = p.id')
            ->innerJoin('ip', 'produtos', 'pr', 'ip.produto_id = pr.id')
            ->where('p.id = ?')
            ->setParameter(0, $pedidoId);
    }

    public function pagarPedido(Pedido $pedido): mixed
    {
        $pedidoAtualizado = $this->_db_connection->atualizar(
            $this->table,
            ['id' => $pedido->getId()],
            [
                'status' => 'Em Preparação'
            ]
        );

        if($pedidoAtualizado === 0) return null;

        return $pedidoAtualizado;
    }

    public function atualizarStatus(Pedido $pedido): mixed
    {
        $pedidoAtualizado = $this->_db_connection->atualizar(
            $this->table,
            ['id' => $pedido->getId()],
            ['status' => $pedido->getStatus()]
        );

        if($pedidoAtualizado === 0) return -2;

        return $pedidoAtualizado;
    }
}