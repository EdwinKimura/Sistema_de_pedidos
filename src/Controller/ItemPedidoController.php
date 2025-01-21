<?php

namespace App\Controller;

use App\Application\Service\ItemPedidoService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use OpenApi\Attributes as OA;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Application\Message\Event\PedidoStatusMessage;

class ItemPedidoController
{
    private ItemPedidoService $service;

    public function __construct(ItemPedidoService $service)
    {
        $this->service = $service;
    }

    #[Route('/api/itemPedidos', methods: ['POST'])]
    #[Nelmio\Areas(['internal'])]
    #[OA\Tag('itensPedido')]
    #[OA\Post(
        summary: "Criar pedido",
        requestBody: new OA\RequestBody(
            description: "Dados do pedido",
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "pedidoId", type: "integer", required: ['true']),
                    new OA\Property(property: "produtoId", type: "integer", required: ['true']),
                    new OA\Property(property: "quantidade", type: "integer", required: ['true'])
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
            )
        ]
    )]
    public function criar(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $itemPedido = $this->service->save($data['pedidoId'], $data['produtoId'], $data['quantidade']);

        return new JsonResponse([
            'id' => $itemPedido->getId(),
            'numero_pedido' => $itemPedido->getPedido()->getId(),
            'item' => $itemPedido->getProduto()->getNome(),
            'valor' => $itemPedido->getProduto()->getPreco(),
            'quantidade' => $itemPedido->getQuantidade(),
        ]);
    }

    #[Route('/api/itensPedido/{id}', methods: ['GET'])]
    #[Nelmio\Areas(['internal'])]
    #[OA\Tag('itensPedido')]
    #[OA\Get(
        summary: "Detalhes do pedido",
        parameters: [
            new OA\Parameter(name: "pedidoId", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Status do pedido",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "pedidoId", type: "integer")
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
    public function listar(int $id): JsonResponse
    {
        $itens = $this->service->listarItensPedido($id);

        return new JsonResponse([
            "itens" => array_map(fn($item) => [
                "item" => $item->getProduto()->getNome(),
                "quantidade" => $item->getQuantidade(),
                "valor" => $item->getProduto()->getPreco()
            ], $itens),
            "Total" => array_reduce($itens, function ($carry, $item) {
                return $carry + ($item->getProduto()->getPreco() * $item->getQuantidade());
            }, 0)
        ]);
    }
}
