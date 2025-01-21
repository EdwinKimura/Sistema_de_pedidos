<?php

namespace App\Controller;

use App\Domain\Entity\Categoria;
use OpenApi\Attributes as OA;
use App\Application\Service\CategoriaService;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class CategoriaController
{
    private CategoriaService $service;

    public function __construct(CategoriaService $service)
    {
        $this->service = $service;
    }

    #[Route('/api/categoria', methods: ['POST'])]
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
        $categoria = $this->service->save($data['nome']);
        
        return new JsonResponse([
            'id' => $categoria->getId(),
            'nome' => $categoria->getNome(),
        ]);
    }

    #[Route('/api/categoria', methods: ['GET'])]
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
    public function listarCategorias(): JsonResponse
    {
        $categorias = $this->service->list();
        if (!$categorias) {
            return new JsonResponse(['message' => 'Categoria não encontrado'], 404);
        }

        return new JsonResponse(array_map(fn(Categoria $categoria) => [
            'id' => $categoria->getId(),
            'nome' => $categoria->getNome()
            ], $categorias)
        );
    }
}