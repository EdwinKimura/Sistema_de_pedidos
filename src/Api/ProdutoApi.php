<?php

namespace App\Api;

use App\Controller\ProdutoController;
use App\Domain\Entity\Produto;
use App\Interfaces\DbConnection;

use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\ModelDescriber\Annotations as Nelmio;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProdutoApi
{
    private DbConnection $_db_connection;

    public function __construct(DbConnection $db_connection)
    {
        $this->_db_connection = $db_connection;
    }

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
        $produtos = ProdutoController::listarTodosOsProdutos($this->_db_connection);

        return new JsonResponse($produtos, 200);
    }

    #[Nelmio\Areas(['internal'])]
    #[OA\Tag('Produtos')]
    #[OA\Get(
        summary: "Listar produtos por categoria",
        parameters: [
            new OA\Parameter(name: "categoria", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de produtos por categoria",
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
    public function listarPorCategoria(int $categoria): JsonResponse
    {
        $produtos = ProdutoController::listarProdutosPorCategoria($this->_db_connection, $categoria);

        if($produtos === null)
        {
            return new JsonResponse(
                ['message' => 'Categoria não registrada.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        return new JsonResponse(
            $produtos,
            JsonResponse::HTTP_OK
        );
    }

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
                    new OA\Property(property: "categoria_id", type: "string")
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
        if (!is_array($data))
        {
           return new JsonResponse(
               ['message' => 'Formato para cadastro inválido.'],
               JsonResponse::HTTP_BAD_REQUEST
           );
        }

        foreach ($data as $key => $produto)
        {
            if (!isset($produto['nome']) || !$produto['nome'] ) return new JsonResponse(
                ['message' => 'Nome do produto '.($key + 1).' deve ser preenchido.'],
                JsonResponse::HTTP_BAD_REQUEST
            );

            if (!isset($produto['descricao']) || !$produto['descricao']) return new JsonResponse(
                ['message' => 'Descrição do produto '.($key + 1).' deve ser preenchida.'],
                JsonResponse::HTTP_BAD_REQUEST
            );

            if (!isset($produto['preco']) || !$produto['preco']) return new JsonResponse(
                ['message' => 'Preço do produto '.($key + 1).' deve ser preenchido.'],
                JsonResponse::HTTP_BAD_REQUEST
            );

            if (!isset($produto['categoria_id']) || !$produto['categoria_id']) return new JsonResponse(
                ['message' => 'ID da categoria do produto '.($key + 1).' deve ser preenchida.'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $produtos = ProdutoController::cadastrarProdutos($this->_db_connection, $data);

        if($produtos === null)
        {
            return new JsonResponse(
                ['message' => 'Erro ao cadastrar, produto já existente. Cadastro abortado!'],
            );
        }

        return new JsonResponse(['message' => 'Produtos cadastrados com sucesso!'], JsonResponse::HTTP_OK);
    }

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
                    new OA\Property(property: "categoria_id", type: "number")
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
        if (!is_array($data))
        {
            return new JsonResponse(
                ['message' => 'Formato para cadastro inválido.'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        if (!isset($data['nome']) || !$data['nome'] ) return new JsonResponse(
            ['message' => 'Nome do produto deve ser preenchido.'],
            JsonResponse::HTTP_BAD_REQUEST
        );

        if (!isset($data['descricao']) || !$data['descricao']) return new JsonResponse(
            ['message' => 'Descrição do produto deve ser preenchida.'],
            JsonResponse::HTTP_BAD_REQUEST
        );

        if (!isset($data['preco']) || !$data['preco']) return new JsonResponse(
            ['message' => 'Preço do produto deve ser preenchido.'],
            JsonResponse::HTTP_BAD_REQUEST
        );

        if (!isset($data['categoria_id']) || !$data['categoria_id']) return new JsonResponse(
            ['message' => 'ID da categoria do produto deve ser preenchida.'],
            JsonResponse::HTTP_BAD_REQUEST
        );

        $produtos = ProdutoController::alterarProduto($this->_db_connection, $data, $id);

        if($produtos === null)
        {
            return new JsonResponse(
                ['message' => 'Erro ao atualizar, produto não encontrando. Alteração abortada!'],
            );
        }

        return new JsonResponse(['message' => 'Produto atualizado com sucesso!'], JsonResponse::HTTP_OK);
    }

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
        $produto = ProdutoController::excluirProduto($this->_db_connection, $id);

        if (!$produto) {
            return new JsonResponse(['error' => 'Produto não encontrado'], JsonResponse::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['message' => 'Produto deletado com sucesso'], JsonResponse::HTTP_OK);
    }
}
