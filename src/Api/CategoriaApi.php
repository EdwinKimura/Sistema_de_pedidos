<?php

namespace App\Api;

use App\Controller\CategoriaController;
use App\Domain\Entity\Categoria;
use App\Interfaces\DbConnection;

use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\ModelDescriber\Annotations as Nelmio;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class CategoriaApi
{
    private DbConnection $_db_connection;

    public function __construct(DbConnection $db_connection){
        $this->_db_connection = $db_connection;
    }

    #[Nelmio\Areas(['internal'])]
    #[OA\Tag('Categoria')]
    #[OA\Get(
        summary: "Listar Categorias",
        responses: [
            new OA\Response(
                response: 201,
                description: "Categoria selecionada",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "nome", type: "string")
                    ]
                )
            )
        ]
    )]
    public function listarCategorias(): JsonResponse{
        $categorias = CategoriaController::obterCategorias($this->_db_connection);
        return new JsonResponse($categorias, 200);
    }

    #[Nelmio\Areas(['internal'])]
    #[OA\Tag('Categoria')]
    #[OA\Post(
        summary: "Cadastrar Categoria",
        requestBody: new OA\RequestBody(
            description: "Nome da categoria",
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "nome", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Categoria cadastrada com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "nome", type: "string")
                    ]
                )
            )
        ]
    )]
    public function cadastrar(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $categoria = new Categoria();
        $categoria->setNome($data['nome']);

        $results = CategoriaController::inserirCategoria($this->_db_connection, $categoria);

        if ($results === null)
        {
            return new JsonResponse(
                ['message' => 'Categoria já existe.'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return new JsonResponse(
            ['message' => 'Categoria inserida com sucesso.'],
            JsonResponse::HTTP_CREATED
        );
    }
}