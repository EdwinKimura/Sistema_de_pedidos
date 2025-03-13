<?php

namespace App\Api;

use App\Controller\PedidoController;
use App\Interfaces\DbConnection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use OpenApi\Attributes as OA;

class PedidoApi
{
    private DbConnection $_db_connection;

    public function __construct(DbConnection $db_connection)
    {
        $this->_db_connection = $db_connection;
    }

    #[Nelmio\Areas(['internal'])]
    #[OA\Tag('Pedidos')]
    #[OA\Get(
        summary: "Listar pedidos",
        responses: [
            new OA\Response(
                response: 200,
                description: "Detalhes do pedido",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "cliente_id", type: "integer"),
                        new OA\Property(property: "status", type: "string"),
                        new OA\Property(property: "valor_total", type: "float"),
                        new OA\Property(property: "criadoEm", type: "string"),
                        new OA\Property(property: "modificadoEm", type: "string"),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Pedido não encontrado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string")
                    ]
                )
            )
        ]
    )]
    public function listar(): JsonResponse
    {
        $pedidos = PedidoController::listarTodos($this->_db_connection);

        return new JsonResponse(
            $pedidos,
            JsonResponse::HTTP_OK,
        );
    }

    #[Nelmio\Areas(['internal'])]
    #[OA\Tag('Pedidos')]
    #[OA\Post(
        summary: "Criar pedido",
        requestBody: new OA\RequestBody(
            description: "Dados do pedido",
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "cpf", type: "string", required: ['false']),
                    new OA\Property(property: "itens", type: "array", items: new OA\Items(properties: [
                        new OA\Property(property: "produto_id", type: "integer"),
                        new OA\Property(property: "quantidade", type: "integer")
                    ]))
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Pedido criado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "status", type: "string"),
                        new OA\Property(property: "valorTotal", type: "number", format: "float")
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Falha ao fazer pedido",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string")
                    ]
                )
            )
        ]
    )]
    public function criar(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $pedido = PedidoController::criarPedido($this->_db_connection, $data);

        if($pedido === -3)
        {
            return new JsonResponse(
                ['message' => 'Não foi possível criar o pedido. Tente novamente.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        if($pedido === -2)
        {
            return new JsonResponse(
                ['message' => 'Produto não encontrado.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        if($pedido === -1)
        {
            return new JsonResponse(
                ['message' => 'Pedido inválido. Tente novamente.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        return new JsonResponse(
            ['message' => 'Pedido criado com sucesso.'],
            JsonResponse::HTTP_CREATED
        );
    }

    #[Nelmio\Areas(['internal'])]
    #[OA\Tag('Pedidos')]
    #[OA\Post(
        summary: "Pagar pedido",
        parameters: [
            new OA\Parameter(name: "pedidoId", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            description: "Pagar pedido pelo id",
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "valor_pago", type: "float", example: 20.4)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Status Atualizado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "status", type: "string")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Status não atualizado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string")
                    ]
                )
            )
        ]
    )]
    public function pagarPedido(Request $request, int $pedidoId): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if(!isset($data['valor_pago'])){
            return new JsonResponse(
                ['message' => 'Campo de valor obrigatório.'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $atualizacaoPagamento = PedidoController::pagarPedido($this->_db_connection, $data['valor_pago'], $pedidoId);

        if($atualizacaoPagamento === null){
            return new JsonResponse(
                ['message' => 'Pedido não encontrado.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        if($atualizacaoPagamento === -1){
            return new JsonResponse(
                ['message' => 'Valor inferior ao da compra.'],
                JsonResponse::HTTP_PAYMENT_REQUIRED
            );
        }

        return new JsonResponse(
            ['message' => "Pedido pago com sucesso. Status: Em Preparação."],
            JsonResponse::HTTP_OK
        );
    }

    #[Nelmio\Areas(['internal'])]
    #[OA\Tag('Pedidos')]
    #[OA\Post(
        summary: "Atualizar pedido",
        requestBody: new OA\RequestBody(
            description: "Status novo do pedido",
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "status", type: "string", example: "Em Preparação")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Status Atualizado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "status", type: "string")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Status não atualizado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string")
                    ]
                )
            )
        ]
    )]
    public function atualizarStatus(Request $request, int $pedidoId): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if(!isset($data['status']))
        {
            return new JsonResponse
            (
                ['message' => 'Status não declarado.'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $pedido = PedidoController::atualizarPedido($this->_db_connection, $pedidoId, $data['status']);

        if($pedido === null)
        {
            return new JsonResponse
            (
                ['message' => 'Pedido não encontrado.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        if($pedido === -1)
        {
            return new JsonResponse
            (
                ['message' => 'Status inválido.'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        if($pedido === -2)
        {
            return new JsonResponse
            (
                ['message' => 'Não foi possível atualizar o pedido.'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return new JsonResponse(
            ['message' => 'Pedido atualizado com sucesso.'],
            JsonResponse::HTTP_OK
        );
    }

    #[Nelmio\Areas(['internal'])]
    #[OA\Tag('Pedidos')]
    #[OA\Get(
        summary: "Acompanhar pedidos",
        responses: [
            new OA\Response(
                response: 200,
                description: "Número, nome e status do pedido",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "nome", type: "string"),
                        new OA\Property(property: "status", type: "string")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Pedido não encontrado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string")
                    ]
                )
            )
        ]
    )]
    public function listarPedidosPendentes(): JsonResponse
    {
        $pedidos = PedidoController::listarTodosPendentes($this->_db_connection);

        return new JsonResponse(
            $pedidos,
            JsonResponse::HTTP_OK,
        );
    }

    #[Nelmio\Areas(['internal'])]
    #[OA\Tag('Pedidos')]
    #[OA\Get(
        summary: "Detalhes do pedido",
        parameters: [
            new OA\Parameter(name: "pedidoId", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Detalhe do pedido",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "cliente", type: "array", items: new OA\Items(properties: [
                            new OA\Property(property: "id", type: "int"),
                            new OA\Property(property: "nome", type: "string"),
                            new OA\Property(property: "email", type: "string"),
                            new OA\Property(property: "cpf", type: "string"),
                        ])),
                        new OA\Property(property: "itens", type: "array", items: new OA\Items(properties: [
                            new OA\Property(property: "id", type: "integer"),
                            new OA\Property(property: "nome", type: "string"),
                            new OA\Property(property: "descricao", type: "string"),
                            new OA\Property(property: "preco", type: "string"),
                            new OA\Property(property: "categoria", type: "string")
                        ])),
                        new OA\Property(property: "status", type: "string"),
                        new OA\Property(property: "criado_em", type: "integer"),
                        new OA\Property(property: "modificado_em", type: "integer"),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Pedido não encontrado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string")
                    ]
                )
            )
        ]
    )]
    public function detalhesPedido(int $pedidoId): JsonResponse
    {
        $pedido = PedidoController::detalhesPedido($this->_db_connection, $pedidoId);

        if($pedido === null)
        {
            return new JsonResponse
            (
                ['message' => 'Pedido não encontrado.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        return new JsonResponse
        (
            $pedido,
            JsonResponse::HTTP_OK,
        );
    }
}
