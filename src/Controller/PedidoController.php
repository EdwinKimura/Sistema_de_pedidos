<?php

namespace App\Controller;

use App\Application\Service\PedidoService;
use App\Application\Service\ClienteService;
use App\Application\Service\ItemPedidoService;
use App\Domain\Entity\Cliente;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use OpenApi\Attributes as OA;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Application\Message\Event\PedidoStatusMessage;

class PedidoController
{
    private PedidoService $service;

    public function __construct(PedidoService $service)
    {
        $this->service = $service;
    }

    #[Route('/api/pedido', methods: ['POST'])]
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
                        new OA\Property(property: "produtoId", type: "integer"),
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
    public function criar(Request $request, ClienteService $clienteService, ItemPedidoService $itemPedidoService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $cliente = $data['cpf'] ? $clienteService->findByCpf($data['cpf']) : null;
        $pedido = $this->service->save($cliente, itens: $data['itens']);
        if(isset($pedido->error) && $pedido->code = 1){
            return new JsonResponse(['message' => $pedido->message], 404);
        }


        $itensPedido = $itemPedidoService->listarItensPedido($pedido->getId());

        return new JsonResponse([
            'pedido' => $pedido->getId(),
            'resumo' => array_map(fn($item) => [
                "item" => $item->getProduto()->getNome(),
                "valor" => $item->getProduto()->getPreco(),
                "quantidade" => $item->getQuantidade()
            ], $itensPedido ),
            'valor_total' => $pedido->getValorTotal()
        ]);
    }

    #[Route('/api/pagamento/{pedidoId}', methods: ['POST'])]
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
        $pedido = $this->service->find($pedidoId);
        if(!$pedido){
            return new JsonResponse([
                'message' => "Pedido não encontrado"
            ], 404);
        }

        $pago = false;
        if($data['valor_pago'] < $pedido->getValorTotal()){
            $pago = false;
            return new JsonResponse([
                'pedido_pago' => $pago,
                'message' => "Valor inferior ao do pedido",
            ]);
        }

        if($data['valor_pago'] == $pedido->getValorTotal()){
            $pedido = $this->service->atualizarStatus($pedidoId, 'Em Preparação');
            $pago = true;
            return new JsonResponse([
                'pedido_pago' => $pago,
                'status' => $pedido->getStatus(),
            ]);
        }

        return new JsonResponse([
            'pedido_pago' => $pago,
            'message' => "Valor pago acima do valor do pedido",
        ]);

    }

    #[Route('/api/pedido/status/{pedidoId}', methods: ['POST'])]
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
    public function atualizarStatus(Request $request, int $pedidoId, MessageBusInterface $bus): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $pedido = $this->service->atualizarStatus($pedidoId, $data['status']);

        return new JsonResponse([
            'id' => $pedido->getId(),
            'status' => $pedido->getStatus(),
        ]);
    }

    #[Route('/api/pedidos', methods: ['GET'])]
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
                        new OA\Property(property: "cliente", type: Cliente::class),
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
        $pedidos = $this->service->listarPedidos();
        return new JsonResponse(array_map(fn($pedido) => [
            'id' => $pedido->getId(),
            'cliente' => [
                'nome' => $pedido->getCliente() ? $pedido->getCliente()->getNome() : null,
                'email' => $pedido->getCliente() ? $pedido->getCliente()->getEmail() : null
            ],
            'status' => $pedido->getStatus(),
            'valor_total' => $pedido->getValorTotal(),
            'criadoEm' => (new \DateTime())->setTimestamp($pedido->getCriadoEm())->format('Y-m-d H:i:s'),
            'modificadoEm' => (new \DateTime())->setTimestamp($pedido->getModificadoEm())->format('Y-m-d H:i:s'),
        ], $pedidos));
    }

    #[Route('/api/acompanhar', methods: ['GET'])]
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
        $pedidos = $this->service->listarPedidosPendentes();
        return new JsonResponse(array_map(fn($pedido) => [
            'id' => $pedido->getId(),
            'nome' => $pedido->getCliente() ? $pedido->getCliente()->getNome() : null,
            'status' => $pedido->getStatus()
        ], $pedidos));
    }
}
