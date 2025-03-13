<?php

namespace App\Controller;

use App\Application\UseCases\ProdutoUseCase;
use App\Gateways\ProdutoGateway;
use App\Interfaces\DbConnection;

class ProdutoController
{

    public static function listarTodosOsProdutos(DbConnection $connection): array
    {
        $produtoGateway = new ProdutoGateway($connection);
        $produtoUseCase = new ProdutoUseCase();

        return $produtoUseCase->obterTodosOsProdutos($produtoGateway);
    }

    public static function listarProdutosPorCategoria(DbConnection $connection, int $categoria): array|null
    {
        $produtoGateway = new ProdutoGateway($connection);
        $produtoUseCase = new ProdutoUseCase();

        return $produtoUseCase->obterProdutosPorCategoria($produtoGateway, $categoria);
    }

    public static function cadastrarProdutos(DbConnection $connection, array $produtos): mixed
    {
        $produtoGateway = new ProdutoGateway($connection);
        $produtoUseCase = new ProdutoUseCase();

        $produtosExistentes = $produtoUseCase->cadastrarProdutos($produtoGateway, $produtos);

        if($produtosExistentes)
        {
            return null;
        }

        return $produtoGateway->cadastrarProdutos($produtos);
    }

    public static function alterarProduto(DbConnection $connection, array $produto, int $id): mixed
    {
        $produtoGateway = new ProdutoGateway($connection);
        $produtoUseCase = new ProdutoUseCase();

        $produtoParaAlterar = $produtoUseCase->alterarProduto($produtoGateway, $id);

        if(!$produtoParaAlterar)
        {
            return null;
        }

        $produtoParaAlterar->setNome($produto['nome']);
        $produtoParaAlterar->setDescricao($produto['descricao']);
        $produtoParaAlterar->setPreco($produto['preco']);
        $produtoParaAlterar->setCategoria($produto['categoria_id']);

        return $produtoGateway->alterarProduto($produtoParaAlterar);
    }

    public static function excluirProduto(DbConnection $connection, int $id): mixed
    {
        $produtoGateway = new ProdutoGateway($connection);
        $produtoUseCase = new ProdutoUseCase();

        $produtoParaDeletar = $produtoUseCase->deletarProduto($produtoGateway, $id);

        if(!$produtoParaDeletar)
        {
            return null;
        }

        return $produtoGateway->excluirProduto($produtoParaDeletar->getId());
    }
}