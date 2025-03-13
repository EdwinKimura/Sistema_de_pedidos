<?php

namespace App\Application\UseCases;

use App\Domain\Entity\Categoria;
use App\Domain\Entity\Cliente;
use App\Domain\Entity\Pedido;
use App\Domain\Entity\Produto;
use App\Gateways\ItemPedidoGateway;
use App\Interfaces\CategoriaGatewayInterface;
use App\Interfaces\ClienteGatewayInterface;
use App\Interfaces\ItemPedidoGatewayInterface;
use App\Interfaces\PedidoGatewayInterface;
use App\Interfaces\ProdutoGatewayInterface;
use stdClass;

class PedidoUseCase
{
    public function criarPedido(
        ClienteGatewayInterface $clienteGateway,
        ProdutoGatewayInterface $produtoGateway,
        array $pedido
    ): Pedido|null
    {
        $novoPedido = new Pedido();
        $novoPedido->setStatus('Recebido');
        $novoPedido->setCriadoEm(strtotime('now'));
        $novoPedido->setModificadoEm(strtotime('now'));

        $cliente = $clienteGateway->obterClientePorCpf($pedido['cpf'])[0]['id'] ?? null;
        $novoPedido->setCliente($cliente);

        $valorTotal = 0;
        foreach ($pedido['itens'] as $produto)
        {
            $buscaProduto = $produtoGateway->obterProdutoPorId($produto['produto_id'])[0] ?? [];
            if (empty($buscaProduto)) return null;

            $valorTotal += $buscaProduto['preco'] * $produto['quantidade'];
        }

        $novoPedido->setItens($pedido['itens']);
        $novoPedido->setValorTotal($valorTotal);

        return $novoPedido;
    }

    public function pagarPedido(PedidoGatewayInterface $pedidoGateway, float $valor, int $pedido): mixed
    {
        $buscaPedido = $pedidoGateway->obterPedidoPorId($pedido);

        if (empty($buscaPedido)) return null;

        $buscaPedido = Pedido::fromArray($buscaPedido);

        if ($buscaPedido->getValorTotal() > $valor) return -1;

        return $buscaPedido;
    }

    public function pedidoValido(PedidoGatewayInterface $pedidoGateway, int $pedidoId, string $status): mixed
    {
        $pedido = $pedidoGateway->obterPedidoPorId($pedidoId);

        if (empty($pedido)) return null;

        $pedido = Pedido::fromArray($pedido);

        $statusValidos = ['Recebido', 'Em Preparação', 'Pronto', 'Finalizado'];
        if(!in_array($status, $statusValidos)) return -1;

        $pedido->setStatus($status);

        return $pedido;
    }

    public function detalhesPedido(
        PedidoGatewayInterface $pedidoGateway,
        ItemPedidoGatewayInterface $itemPedidoGateway,
        ClienteGatewayInterface $clienteGateway,
        ProdutoGatewayInterface $produtoGateway,
        CategoriaGatewayInterface $categoriaGateway,
        int $pedidoId
    ): mixed
    {
        $pedido = $pedidoGateway->obterPedidoPorId($pedidoId);
        if (empty($pedido)) return null;

        $pedido = Pedido::fromArray($pedido);

        $cliente = $clienteGateway->obterClientePorId($pedido->getCliente())[0];
        $cliente = Cliente::fromArray($cliente);

        $itensPedido = $itemPedidoGateway->obterTodosItensPorPedido($pedido->getId());

        $detalhesPedido                 = new \stdClass();
        $detalhesPedido->id             = $pedido->getId();
        $detalhesPedido->cliente        = new stdClass();
        $detalhesPedido->cliente->id    = $cliente->getId();
        $detalhesPedido->cliente->nome  = $cliente->getNome();
        $detalhesPedido->cliente->cpf   = $cliente->getCpf();
        $detalhesPedido->cliente->email = $cliente->getEmail();

        foreach ($itensPedido as $key => $itemPedido)
        {
            $produto = $produtoGateway->obterProdutoPorId($itemPedido['produto_id'])[0];
            $produto = Produto::fromArray($produto);

            $detalhesPedido->itens[$key] = new \stdClass();
            $detalhesPedido->itens[$key]->id = $produto->getId();
            $detalhesPedido->itens[$key]->nome = $produto->getNome();
            $detalhesPedido->itens[$key]->descricao = $produto->getDescricao();
            $detalhesPedido->itens[$key]->preco = $produto->getPreco();

            $categoria = $categoriaGateway->buscarCategoriaPorParametros(['id' => $produto->getCategoria()])[0];
            $categoria = Categoria::fromArray($categoria);

            $detalhesPedido->itens[$key]->categoria = $categoria->getNome();
        }

        $detalhesPedido->valor_total    = $pedido->getValorTotal();
        $detalhesPedido->status         = $pedido->getStatus();
        $detalhesPedido->criado_em      = $pedido->getCriadoEm();
        $detalhesPedido->modificado_em  = $pedido->getModificadoEm();

        return $detalhesPedido;
    }
}