<?php

namespace App\Domain\Entity;

class Categoria
{
    private ?int $id;
    private ?string $nome;

    public function __construct(?int $id = null, ?string $nome = null)
    {
        $this->id = $id;
        $this->nome = $nome;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): static
    {
        $this->nome = $nome;

        return $this;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['nome'] ?? null
        );
    }
}
