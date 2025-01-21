<?php

namespace App\Controller;

use App\Domain\Entity\Cliente;
use OpenApi\Attributes as OA;
use App\Application\Service\ClienteService;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClienteController
{
    private ClienteService $service;

    public function __construct(ClienteService $service)
    {
        $this->service = $service;
    }

    #[Route('/api/clientes', methods: ['POST'])]
    #[Nelmio\Areas(['internal'])]
    #[OA\Tag('Clientes')]
    #[OA\Post(
        summary: "Cadastrar cliente",
        requestBody: new OA\RequestBody(
            description: "Dados do cliente",
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "nome", type: "string"),
                    new OA\Property(property: "email", type: "string"),
                    new OA\Property(property: "cpf", type: "string")
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
        $cliente = $this->service->save($data['cpf'] ?? null, $data['nome'] ?? null, $data['email'] ?? null);
        
        return new JsonResponse([
            'id' => $cliente->getId(),
            'nome' => $cliente->getNome(),
            'email' => $cliente->getEmail()
        ]);
    }

    #[Route('/api/clientes', methods: ['GET'])]
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
        $clientes = $this->service->list();
        if (!$clientes) {
            return new JsonResponse(['message' => 'Cliente não encontrado'], 404);
        }

        return new JsonResponse(array_map(fn(Cliente $cliente) => [
            'id' => $cliente->getId(),
            'nome' => $cliente->getNome(),
            'email' => $cliente->getEmail()
            ], $clientes)
        );
    }
}