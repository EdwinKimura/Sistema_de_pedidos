<?php

namespace App\External;

use App\Interfaces\DbConnection;
use Doctrine\DBAL\Connection;
use Aws\Lambda\LambdaClient;
use Aws\Exception\AwsException;

class Database implements DbConnection
{
    private $connection;

    public function __construct(Connection $connection){
        $this->connection = $connection;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    public function listarTodos(string $tabela, array $campos = ['*'], array $order = ['id' => 'ASC']): mixed
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->select($campos)->from($tabela);

        foreach ($order as $key => $value)
        {
            $queryBuilder->addOrderBy($key, $value);
        }

        $results = $queryBuilder->executeQuery()->fetchAllAssociative();

        return $results;
    }

    public function listarPorParametros(string $tabela, array $condicao, array $campos = ['*'], array $order = ['id' => 'ASC'], string $modoCondicao = ' = '): mixed
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->select($campos)->from($tabela);

        foreach ($condicao as $key => $value) {
            $queryBuilder->andWhere($key . $modoCondicao . ':' . $key)->setParameter($key, $value);
        }

        foreach ($order as $key => $value)
        {
            $queryBuilder->addOrderBy($key, $value);
        }

        $results = $queryBuilder->executeQuery()->fetchAllAssociative();

        return $results;
    }

    public function inserir(string $tabela, array $valores, bool $idCriado = false): mixed
    {
        $this->connection->beginTransaction();

        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->insert($tabela);

        foreach ($valores as $key => $value) {
            $queryBuilder->setValue($key, ':' . $key)->setParameter($key, $value);
        }

        $results = $queryBuilder->executeStatement();

        if($idCriado)
        {
            $results = $this->connection->lastInsertId();
        }

        $this->connection->commit();

        return $results;
    }

    public function deletar(string $tabela, array $condicao): mixed
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->delete($tabela);

        foreach ($condicao as $key => $value) {
            $queryBuilder->andWhere($key . ' = :' . $key)->setParameter($key, $value);
        }

        $results = $queryBuilder->executeStatement();

        return $results;
    }

    public function atualizar(string $tabela, array $condicao, array $valores): mixed
    {
        $this->connection->beginTransaction();

        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->update($tabela);

        foreach ($valores as $key => $value) {
            $queryBuilder->set($key, ':' . $key)->setParameter($key, $value);
        }

        foreach ($condicao as $key => $value) {
            $queryBuilder->andWhere($key . ' = :' . $key)->setParameter($key, $value, \PDO::PARAM_INT);
        }


        $results = $queryBuilder->orderBy('id', 'ASC')->executeStatement();

        $this->connection->commit();

        return $results;
    }

    public function listarViaLambda(string $cpf): mixed
    {
        $client = new LambdaClient([
            'region' => 'sa-east-1',
            'version' => 'latest',
            'credentials' => [
                'key'    => getenv('AWS_ACCESS_KEY_ID'),
                'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        $payload = json_encode(['cpf' => $cpf]);

        try {
            $result = $client->invoke([
                'FunctionName'   => 'auth-cpf-lambda',
                'InvocationType' => 'RequestResponse',
                'Payload'        => $payload,
            ]);

            $resposta = json_decode((string) $result->get('Payload'), true);
            return $resposta;
        } catch (AwsException $e) {
            error_log("Erro Lambda: " . $e->getMessage());
            return null;
        }
    }
}