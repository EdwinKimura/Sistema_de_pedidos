<?php

namespace App\Interfaces;

interface DbConnection
{
    public function listarTodos(string $tabela, array $campos, array $order): mixed;

    public function listarPorParametros(string $tabela, array $condicao, array $campos, array $order, string $modoCondicao): mixed;

    public function inserir(string $tabela, array $valores, bool $idCriado): mixed;

    public function deletar(string $tabela, array $condicao): mixed;

    public function atualizar(string $tabela, array $condicao, array $valores): mixed;

    public function getConnection(): mixed;
}