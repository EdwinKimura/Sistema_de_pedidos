<?php

namespace App\Interfaces;

use App\Domain\Entity\Categoria;

interface CategoriaGatewayInterface
{
    public function buscarTodasAsCategoria(): mixed;
    public function buscarCategoriaPorNome(string $categoria): mixed;
    public function inserirNovaCategoria(Categoria $categoria): mixed;
    public function buscarCategoriaPorParametros(array $parametros): mixed;
}