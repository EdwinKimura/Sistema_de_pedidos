<?php

namespace App\Domain\Entity;

use App\Infrastructure\Repository\PedidoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpseclib3\Math\BigInteger;

#[ORM\Entity(repositoryClass: PedidoRepository::class)]
class Pedido
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'pedidos', cascade: ['persist', 'remove'])]
    private ?Cliente $cliente = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column()]
    private ?float $valorTotal = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $criadoEm = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $modificadoEm = null;

    /**
     * @var Collection<int, ItemPedido>
     */
    #[ORM\OneToMany(targetEntity: ItemPedido::class, mappedBy: 'pedido', cascade: ['persist', 'remove'])]
    private Collection $itemPedidos;

    public function __construct()
    {
        $this->itemPedidos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCliente(): ?Cliente
    {
        return $this->cliente;
    }

    public function setCliente(?Cliente $cliente): static
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

    /**
     * @return Collection<int, ItemPedido>
     */
    public function getItemPedidos(): Collection
    {
        return $this->itemPedidos;
    }

    public function addItemPedido(ItemPedido $itemPedido): static
    {
        if (!$this->itemPedidos->contains($itemPedido)) {
            $this->itemPedidos->add($itemPedido);
            $itemPedido->setPedido($this);
        }

        return $this;
    }

    public function removeItemPedido(ItemPedido $itemPedido): static
    {
        if ($this->itemPedidos->removeElement($itemPedido)) {
            // set the owning side to null (unless already changed)
            if ($itemPedido->getPedido() === $this) {
                $itemPedido->setPedido(null);
            }
        }

        return $this;
    }
}
