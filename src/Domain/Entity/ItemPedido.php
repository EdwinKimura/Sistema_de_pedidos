<?php

namespace App\Domain\Entity;

use App\Infrastructure\Repository\ItemPedidoRepository;

class ItemPedido
{
    private ?int $id = null;

    private ?int $pedido = null;

    private ?int $produto = null;

    private ?int $quantidade = null;

    public function __construct(?int $id = null, ?int $pedido = null, ?int $produto = null, ?int $quantidade = null)
    {
        $this->id = $id;
        $this->pedido = $pedido;
        $this->produto = $produto;
        $this->quantidade = $quantidade;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPedido(): ?int
    {
        return $this->pedido;
    }

    public function setPedido(?int $pedido): static
    {
        $this->pedido = $pedido;

        return $this;
    }

    public function getProduto(): ?int
    {
        return $this->produto;
    }

    public function setProduto(?int $produto): static
    {
        $this->produto = $produto;

        return $this;
    }

    public function getQuantidade(): ?int
    {
        return $this->quantidade;
    }

    public function setQuantidade(int $quantidade): static
    {
        $this->quantidade = $quantidade;

        return $this;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['pedido'] ?? null,
            $data['produto'] ?? null,
            $data['quantidade'] ?? null
        );
    }
}
