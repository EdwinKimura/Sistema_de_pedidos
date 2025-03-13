<?php

namespace App\Gateways;
use App\Interfaces\CategoriaGatewayInterface;
use App\Interfaces\DbConnection;
use App\Domain\Entity\Categoria;

class CategoriaGateway implements CategoriaGatewayInterface
{
    private DbConnection $connection;
    private string $table = 'categorias';

    public function __construct(DbConnection $connection){
        $this->connection = $connection;
    }

    public function buscarTodasAsCategoria(): mixed
    {
        return $this->connection->listarTodos($this->table);
    }

    public function buscarCategoriaPorNome(string $categoria): mixed
    {
        return $this->connection->listarPorParametros($this->table, ['nome' => $categoria]);
    }

    public function buscarCategoriaPorParametros(array $parametros): mixed
    {
        return $this->connection->listarPorParametros($this->table, $parametros);
    }

    public function inserirNovaCategoria(Categoria $categoria): mixed
    {
        $valores = ['nome' => $categoria->getNome()];

        return $this->connection->inserir($this->table, $valores);
    }
}