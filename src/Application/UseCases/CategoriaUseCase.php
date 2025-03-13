<?php

namespace App\Application\UseCases;

use App\Domain\Entity\Categoria;
use App\Interfaces\CategoriaGatewayInterface;

class CategoriaUseCase
{
    public function obterTodasAsCategorias(CategoriaGatewayInterface $categoriaGateway): array
    {
        return $categoriaGateway->buscarTodasAsCategoria();
    }

    public function cadastrarNovaCategoria(
        CategoriaGatewayInterface $categoriaGateway,
        string $categoria
    ): Categoria|null
    {
        $resultado = $categoriaGateway->buscarCategoriaPorNome($categoria);

        if ($resultado) return null;

        $novaCategoria = new Categoria();
        $novaCategoria->setNome($categoria);

        return $novaCategoria;
    }
}