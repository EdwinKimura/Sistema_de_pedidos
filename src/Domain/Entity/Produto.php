<?php

namespace App\Domain\Entity;

use App\Infrastructure\Repository\ProdutoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProdutoRepository::class)]
class Produto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nome = null;

    #[ORM\Column(length: 255)]
    private ?string $descricao = null;

    #[ORM\Column]
    private ?float $preco = null;

    #[ORM\ManyToOne(inversedBy: 'produtos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categoria $categoria = null;

    /**
     * @var Collection<int, ItemPedido>
     */
    #[ORM\OneToMany(targetEntity: ItemPedido::class, mappedBy: 'produto')]
    private Collection $itemPedidos;

    public function __construct()
    {
        $this->itemPedidos = new ArrayCollection();
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

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): static
    {
        $this->descricao = $descricao;

        return $this;
    }

    public function getPreco(): ?float
    {
        return $this->preco;
    }

    public function setPreco(float $preco): static
    {
        $this->preco = $preco;

        return $this;
    }

    public function getCategoria(): ?Categoria
    {
        return $this->categoria;
    }

    public function setCategoria(?Categoria $categoria): static
    {
        $this->categoria = $categoria;

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
            $itemPedido->setProduto($this);
        }

        return $this;
    }

    public function removeItemPedido(ItemPedido $itemPedido): static
    {
        if ($this->itemPedidos->removeElement($itemPedido)) {
            // set the owning side to null (unless already changed)
            if ($itemPedido->getProduto() === $this) {
                $itemPedido->setProduto(null);
            }
        }

        return $this;
    }
}
