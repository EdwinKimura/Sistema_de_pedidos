<?php

namespace App\Interfaces;

use App\Domain\Entity\Produto;

interface ProdutoGatewayInterface
{
    public function obterTodosOsProdutos(): array|null;
    public function obterProdutosPorNome(string $nome): array;
    public function obterProdutoPorId(int $id): array;
    public function obterProdutosPorCategoria(int $categoria): array;
    public function cadastrarProdutos(array $produtos): mixed;
    public function alterarProduto(Produto $produto): mixed;
    public function excluirProduto(int $id): mixed;
}