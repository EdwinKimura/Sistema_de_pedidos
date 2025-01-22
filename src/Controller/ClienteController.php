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
        $cliente = $this->service->save($data['cpf'], $data['nome'], $data['email']);
        
        return new JsonResponse([
            'id' => $cliente->getId(),
            'nome' => $cliente->getNome(),
            'email' => $cliente->getEmail(),
            'cpf' => $cliente->getCpf()
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
                'email' => $cliente->getEmail(),
                'cpf' => $cliente->getCpf()
            ], $clientes)
        );
    }

    #[Route('/api/cliente/{cpf}', methods: ['GET'])]
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
        $cliente = $this->service->findByCpf($cpf);
        if (!$cliente) {
            return new JsonResponse(['message' => 'Cliente não encontrado'], 404);
        }

        return new JsonResponse([
            'id' => $cliente->getId(),
            'nome' => $cliente->getNome(),
            'email' => $cliente->getEmail(),
            'cpf' => $cliente->getCpf()
        ]);
    }
}