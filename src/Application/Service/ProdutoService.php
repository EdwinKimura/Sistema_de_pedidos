<?php

namespace App\Application\Service;

use App\Domain\Entity\Produto;
use App\Infrastructure\Repository\ProdutoRepository;
use App\Infrastructure\Repository\CategoriaRepository;

class ProdutoService
{
    private ProdutoRepository $repository;
    private CategoriaRepository $categoriaRepository;

    public function __construct(ProdutoRepository $repository, CategoriaRepository $categoriaRepository)
    {
        $this->repository = $repository;
        $this->categoriaRepository = $categoriaRepository;
    }

    public function criarProduto(string $nome, string $descricao, float $preco, int $categoriaId): Produto
    {
        $produto = new Produto();
        $produto->setNome($nome);
        $produto->setDescricao($descricao);
        $produto->setPreco($preco);
        $produto->setCategoria($this->categoriaRepository->find($categoriaId));
        return $this->repository->save($produto);
    }

    public function atualizarProduto(int $id, string $nome, string $descricao, float $preco, int $categoriaId): ?Produto
    {
        $produto = $this->repository->find($id);

        if (!$produto) {
            return null;
        }

        $produto->setNome($nome ?? $produto->getNome());
        $produto->setDescricao($descricao ?? $produto->getDescricao());
        $produto->setPreco($preco ?? $produto->getPreco());

        if($categoriaId){
            $produto->setCategoria($this->categoriaRepository->find($categoriaId));
        }

        return $this->repository->save($produto);
    }

    public function buscarProduto($id): Produto
    {
        return $this->repository->find($id);
    }

    public function listarProdutos(): array
    {
        return $this->repository->findAll();
    }

    public function deletarProduto(int $id): bool
    {
        $produto = $this->repository->find($id);

        if (!$produto) {
            return false;
        }

        $this->repository->delete($produto);
        return true;
    }
}