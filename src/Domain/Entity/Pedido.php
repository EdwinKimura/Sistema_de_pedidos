<?php

namespace App\Domain\Entity;

use Doctrine\Common\Collections\Collection;

class Pedido
{
    private ?int $id = null;

    private ?int $cliente = null;

    private ?array $itens = null;

    private ?float $valorTotal = null;

    private ?string $status = null;

    private ?int $criadoEm = null;

    private ?int $modificadoEm = null;

    public function __construct(
        ?int $id = null,
        ?int $cliente = null,
        ?array $itens = null,
        ?string $status = null,
        ?float $valorTotal = null,
        ?int $criadoEm = null,
        ?int $modificadoEm = null
    )
    {
        $this->id = $id;
        $this->cliente = $cliente;
        $this->status = $status;
        $this->valorTotal = $valorTotal;
        $this->criadoEm = $criadoEm;
        $this->modificadoEm = $modificadoEm;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCliente(): ?int
    {
        return $this->cliente;
    }

    public function setCliente(?int $cliente): static
    {
        $this->cliente = $cliente;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getValorTotal(): ?float
    {
        return $this->valorTotal;
    }

    public function setValorTotal(string $valorTotal): static
    {
        $this->valorTotal = $valorTotal;

        return $this;
    }

    public function getItens(): ?array
    {
        return $this->itens;
    }

    public function setItens(?array $itens): static
    {
        $this->itens = $itens;

        return $this;
    }

    public function getCriadoEm(): ?int
    {
        return $this->criadoEm;
    }

    public function setCriadoEm(int $criadoEm): static
    {
        $this->criadoEm = $criadoEm;

        return $this;
    }

    public function getModificadoEm(): ?int
    {
        return $this->modificadoEm;
    }

    public function setModificadoEm(int $modificadoEm): static
    {
        $this->modificadoEm = $modificadoEm;

        return $this;
    }

    public static function fromArray(array $data): static
    {
        return new self(
            $data['id'] ?? null,
            $data['cliente_id'] ?? null,
            $data['itens'] ?? null,
            $data['status'] ?? null,
            $data['valor_total'] ?? null,
            $data['criado_em'] ?? null,
            $data['modificado_em'] ?? null
        );
    }

}
