<?php

namespace App\Domain\Entity;

use App\Infrastructure\Repository\ClienteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Cliente
{
    private ?int $id = null;

    private ?string $nome = null;

    private ?string $email = null;

    private ?string $cpf = null;

    private ?Pedido $pedido = null;

    public function __construct(
        ?int $id = null,
        ?string $nome = null,
        ?string $email = null,
        ?string $cpf = null
    )
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->email = $email;
        $this->cpf = $cpf;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(?string $nome): static
    {
        $this->nome = $nome;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getCpf(): ?string
    {
        return $this->cpf;
    }

    public function setCpf(?string $cpf): static
    {
        $this->cpf = $cpf;

        return $this;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['nome'] ?? null,
            $data['email'] ?? null,
            $data['cpf'] ?? null
        );
    }

}
