<?php

namespace App\Controller;

use App\Application\UseCases\CategoriaUseCase;
use App\Domain\Entity\Categoria;
use App\Interfaces\DbConnection;
use App\Gateways\CategoriaGateway;

class CategoriaController
{
    public static function obterCategorias(DbConnection $db_connection): mixed{
        $categoriaGateway = new CategoriaGateway($db_connection);
        $categoriaUseCase = new CategoriaUseCase();

        $categorias = $categoriaUseCase->obterTodasAsCategorias($categoriaGateway);

        return $categorias;
    }

    public static function inserirCategoria(DbConnection $db_connection, Categoria $categoria): mixed
    {
        $categoriaGateway = new CategoriaGateway($db_connection);
        $categoriaUseCase = new CategoriaUseCase();

        $novaCategoria = $categoriaUseCase->cadastrarNovaCategoria($categoriaGateway, $categoria->getNome());

        if ($novaCategoria === null)
        {
            return null;
        }

        return $categoriaGateway->inserirNovaCategoria($categoria);
    }
}