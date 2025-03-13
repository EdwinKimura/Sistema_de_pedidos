<?php

namespace App\Api;

use App\Application\UseCases\ClienteUseCase;
use App\Domain\Entity\Cliente;
use App\Controller\ClienteController;
use App\Gateways\ClienteGateway;
use App\Interfaces\DbConnection;

use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\ModelDescriber\Annotations as Nelmio;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClienteApi
{
    private DbConnection $_db_connection;

    public function __construct(DbConnection $db_connection){
        $this->_db_connection = $db_connection;
    }

    #[Nelmio\Areas(['internal'])]
    #[OA\Tag('Clientes')]
    #[OA\Get(
        summary: "Listar Clientes",
        responses: [
            new OA\Response(
                response: 201,
                description: "Cliente selecionado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "nome", type: "string"),
                        new OA\Property(property: "email", type: "string")
                    ]
                )
            )
        ]
    )]
    public function listarClientes(): JsonResponse
    {
        $clientes = ClienteController::listarTodosOsClientes($this->_db_connection);

        return new JsonResponse($clientes,  200);
    }

    #[Nelmio\Areas(['internal'])]
    #[OA\Tag('Clientes')]
    #[OA\Get(
        summary: "Buscar Cliente",
        parameters: [
            new OA\Parameter(name: "cpf", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(
                response: 201,
                description: "Cliente selecionado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "nome", type: "string"),
                        new OA\Property(property: "email", type: "string")
                    ]
                )
            )
        ]
    )]
    public function buscarPorCpf(string $cpf): JsonResponse
    {
        $cliente = ClienteController::obterClientePorCpf($this->_db_connection, $cpf);

        if (!$cliente) {
            return new JsonResponse(['message' => 'Cliente não encontrado'], 404);
        }

        return new JsonResponse($cliente, 200);
    }

    #[Nelmio\Areas(['internal'])]
    #[OA\Tag('Clientes')]
    #[OA\Post(
        summary: "Cadastrar cliente",
        requestBody: new OA\RequestBody(
            description: "Dados do cliente",
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "nome", type: "string", required: ['true']),
                    new OA\Property(property: "email", type: "string", required: ['true']),
                    new OA\Property(property: "cpf", type: "string", required: ['true'])
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Cliente cadastrado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "nome", type: "string"),
                        new OA\Property(property: "email", type: "string"),
                        new OA\Property(property: "cpf", type: "string")
                    ]
                )
            )
        ]
    )]
    public function cadastrar(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if(!isset($data['cpf']) || !isset($data['nome']) || !isset($data['email'])){
            return new JsonResponse([
                "message" => "Campos cpf, nome e e-mail obrigatórios",
            ]);
        }

        $novoCliente = ClienteController::cadastrarCliente($this->_db_connection, $data);

        if($novoCliente === null)
        {
            return new JsonResponse(
                ['message' => 'Cliente já existe.'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
        
        return new JsonResponse(
            ['message' => 'Cliente cadastrado com sucesso.'],
            JsonResponse::HTTP_CREATED
        );
    }
}