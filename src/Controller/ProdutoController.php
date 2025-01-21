<?php

namespace App\Controller;

use App\Domain\Entity\Produto;
use App\Application\Service\ProdutoService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use OpenApi\Attributes as OA;

class ProdutoController
{
    private ProdutoService $service;

    public function __construct(ProdutoService $service)
    {
        $this->service = $service;
    }

    #[Route('/api/produtos', methods: ['POST'])]
    #[Nelmio\Areas(['internal'])]
    #[OA\Tag('Produtos')]
    #[OA\Post(
        summary: "Criar um novo produto",
        requestBody: new OA\RequestBody(
            description: "Dados do produto a ser criado",
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "nome", type: "string"),
                    new OA\Property(property: "descricao", type: "string"),
                    new OA\Property(property: "preco", type: "number", format: "float"),
                    new OA\Property(property: "categoria", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Produto criado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "nome", type: "string"),
                        new OA\Property(property: "descricao", type: "string"),
                        new OA\Property(property: "preco", type: "number", format: "float"),
                        new OA\Property(property: "categoria", type: "string")
                    ]
                )
            )
        ]
    )]
    public function criar(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $produto = $this->service->criarProduto($data['nome'], $data['descricao'], $data['preco'], $data['categoria']);

        return new JsonResponse([
            'id' => $produto->getId(),
            'nome' => $produto->getNome(),
            'descricao' => $produto->getDescricao(),
            'preco' => $produto->getPreco(),
            'categoria' => $produto->getCategoria()->getNome(),
        ], 201);
    }

    #[Route('/api/produtos/{id}', methods: ['PUT'])]
    #[Nelmio\Areas(['internal'])]
    #[OA\Tag('Produtos')]
    #[OA\Put(
        summary: "Atualizar um produto",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            description: "Dados do produto a serem atualizados",
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "nome", type: "string"),
                    new OA\Property(property: "descricao", type: "string"),
                    new OA\Property(property: "preco", type: "number", format: "float"),
                    new OA\Property(property: "categoria", type: "number")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Produto atualizado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "nome", type: "string"),
                        new OA\Property(property: "descricao", type: "string"),
                        new OA\Property(property: "preco", type: "number", format: "float"),
                        new OA\Property(property: "categoria", type: "string")
                    ]
                )
            )
        ]
    )]
    public function atualizar(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $produto = $this->service->atualizarProduto(
            $id,
            $data['nome'] ?? null,
            $data['descricao'] ?? null,
            $data['preco'] ?? null,
            $data['categoria'] ?? null
        );

        if (!$produto) {
            return new JsonResponse(['error' => 'Produto não encontrado'], 404);
        }

        return new JsonResponse([
            'id' => $produto->getId(),
            'nome' => $produto->getNome(),
            'descricao' => $produto->getDescricao(),
            'preco' => $produto->getPreco(),
            'categoria' => $produto->getCategoria()->getNome(),
        ]);
    }

    #[Route('/api/produtos', methods: ['GET'])]
    #[Nelmio\Areas(['internal'])]
    #[OA\Tag('Produtos')]
    #[OA\Get(
        summary: "Listar todos os produtos",
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de produtos",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "integer"),
                            new OA\Property(property: "nome", type: "string"),
                            new OA\Property(property: "descricao", type: "string"),
                            new OA\Property(property: "preco", type: "number", format: "float"),
                            new OA\Property(property: "categoria", type: "string")
                        ]
                    )
                )
            )
        ]
    )]
    public function listar(): JsonResponse
    {
        $produtos = $this->service->listarProdutos();

        return new JsonResponse(array_map(fn(Produto $produto) => [
            'id' => $produto->getId(),
            'nome' => $produto->getNome(),
            'descricao' => $produto->getDescricao(),
            'preco' => $produto->getPreco(),
            'categoria' => $produto->getCategoria()->getNome(),
        ], $produtos));
    }

    #[Route('/api/produtos/{id}', methods: ['DELETE'])]
    #[Nelmio\Areas(['internal'])]
    #[OA\Tag('Produtos')]
    #[OA\Delete(
        summary: "Deletar um produto",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Produto deletado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Produto não encontrado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string")
                    ]
                )
            )
        ]
    )]
    public function deletar(int $id): JsonResponse
    {
        $produto = $this->service->buscarProduto($id);

        if (!$produto) {
            return new JsonResponse(['error' => 'Produto não encontrado'], 404);
        }

        $this->service->deletarProduto($produto->getId());

        return new JsonResponse(['message' => 'Produto deletado com sucesso']);
    }
}
