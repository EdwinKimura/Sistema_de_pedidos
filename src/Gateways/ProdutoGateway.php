<?php

namespace App\Gateways;

use App\Domain\Entity\Categoria;
use App\Domain\Entity\Produto;
use App\Interfaces\ProdutoGatewayInterface;
use App\Interfaces\DbConnection;

class ProdutoGateway implements ProdutoGatewayInterface
{
    private DbConnection $_db_connection;
    private string $table = 'produtos';

    public function __construct(DbConnection $_db_connection){
        $this->_db_connection = $_db_connection;
    }
    public function obterTodosOsProdutos(): array|null
    {
        return $this->_db_connection->listarTodos($this->table);
    }

    public function obterProdutosPorNome(string $nome): array
    {
        $produto = $this->_db_connection->listarPorParametros($this->table, ['nome' => $nome]);
        return $produto;
    }

    public function obterProdutoPorId(int $id): array
    {
        $produto = $this->_db_connection->listarPorParametros($this->table, ['id' => $id]);
        return $produto;
    }

    public function obterProdutosPorCategoria(int $categoria): array
    {
        $queryBuilder = $this->_db_connection->getConnection()->createQueryBuilder();
        $results = $queryBuilder->select('produtos.id', 'produtos.nome', 'produtos.descricao', 'produtos.preco', 'categorias.nome as nome_categoria')
            ->from($this->table)
            ->innerJoin('produtos', 'categorias', 'categorias', 'produtos.categoria_id = categorias.id')
            ->where('categorias.id = :id')
            ->orderBy('produtos.id', 'ASC')
            ->setParameter('id', $categoria)
            ->fetchAllAssociative();

        return $results;
    }

    public function cadastrarProdutos(array $produtos): mixed
    {
        $produtosCadastrados = 0;
        foreach ($produtos as $key => $produto)
        {
            $produtosCadastrados += $this->_db_connection->inserir($this->table, $produto);
        }

        if ($produtosCadastrados === 0) return null;

        return true;
    }

    public function alterarProduto(Produto $produto): mixed
    {
        $produtoAlterado = $this->_db_connection->atualizar(
            $this->table,
            ['id' => $produto->getId()],
            ['nome' => $produto->getNome(), 'descricao' => $produto->getDescricao(), 'preco' => $produto->getPreco()]
        );

        if ($produtoAlterado === 0) return null;

        return true;
    }

    public function excluirProduto(int $id): mixed
    {
        $produtoDeletado = $this->_db_connection->deletar($this->table, ['id' => $id]);

        if ($produtoDeletado === 0) return null;

        return true;
    }
}