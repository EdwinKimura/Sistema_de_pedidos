<?php

namespace App\Application\UseCases;

use App\Domain\Entity\Produto;
use App\Interfaces\ProdutoGatewayInterface;

class ProdutoUseCase
{
    public function obterTodosOsProdutos(ProdutoGatewayInterface $produtoGateway): array
    {
        return $produtoGateway->obterTodosOsProdutos();
    }

    public function obterProdutosPorCategoria(
        ProdutoGatewayInterface $produtoGateway,
        int $categoria
    ): array|null
    {
        $produtos = $produtoGateway->obterProdutosPorCategoria($categoria);

        if(!$produtos) return null;

        return $produtos;
    }

    public function cadastrarProdutos(ProdutoGatewayInterface $produtoGateway, array $produtos): mixed
    {
        $produtosExistentes = [];
        foreach ($produtos as $produto)
        {
             $busca = $produtoGateway->obterProdutosPorNome($produto['nome']);
             if(!empty($busca)) array_push($produtosExistentes, $busca[0]);
        }

        return (!empty($produtosExistentes));
    }

    public function alterarProduto(ProdutoGatewayInterface $produtoGateway, int $id): Produto
    {
        $busca = $produtoGateway->obterProdutoPorId($id);

        return Produto::fromArray($busca[0]);
    }

    public function deletarProduto(ProdutoGatewayInterface $produtoGateway, int $id): Produto
    {
        $busca = $produtoGateway->obterProdutoPorId($id);

        return Produto::fromArray($busca[0]);
    }

}