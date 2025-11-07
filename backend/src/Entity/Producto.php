<?php

namespace App\Entity;

use App\Repository\ProductoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductoRepository::class)]
class Producto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?float $precio = null;

    #[ORM\Column(type: 'integer')]
    private ?int $stock = null;

    #[ORM\ManyToOne(inversedBy: 'productos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categoria $categoria = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagenUrl = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $activo = true;

    /**
     * @var Collection<int, PedidoDetalle>
     */
    #[ORM\OneToMany(targetEntity: PedidoDetalle::class, mappedBy: 'producto')]
    private Collection $pedidoDetalles;

    /**
     * @var Collection<int, Resena>
     */
    #[ORM\OneToMany(targetEntity: Resena::class, mappedBy: 'producto')]
    private Collection $resenas;

    public function __construct()
    {
        $this->pedidoDetalles = new ArrayCollection();
        $this->resenas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): static
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getPrecio(): ?float
    {
        return $this->precio;
    }

    public function setPrecio(float $precio): static
    {
        $this->precio = $precio;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

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

    public function getImagenUrl(): ?string
    {
        return $this->imagenUrl;
    }

    public function setImagenUrl(?string $imagenUrl): static
    {
        $this->imagenUrl = $imagenUrl;

        return $this;
    }

    public function isActivo(): ?bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): static
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * @return Collection<int, PedidoDetalle>
     */
    public function getPedidoDetalles(): Collection
    {
        return $this->pedidoDetalles;
    }

    public function addPedidoDetalle(PedidoDetalle $pedidoDetalle): static
    {
        if (!$this->pedidoDetalles->contains($pedidoDetalle)) {
            $this->pedidoDetalles->add($pedidoDetalle);
            $pedidoDetalle->setProducto($this);
        }

        return $this;
    }

    public function removePedidoDetalle(PedidoDetalle $pedidoDetalle): static
    {
        if ($this->pedidoDetalles->removeElement($pedidoDetalle)) {
            // set the owning side to null (unless already changed)
            if ($pedidoDetalle->getProducto() === $this) {
                $pedidoDetalle->setProducto(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Resena>
     */
    public function getResenas(): Collection
    {
        return $this->resenas;
    }

    public function addResena(Resena $resena): static
    {
        if (!$this->resenas->contains($resena)) {
            $this->resenas->add($resena);
            $resena->setProducto($this);
        }

        return $this;
    }

    public function removeResena(Resena $resena): static
    {
        if ($this->resenas->removeElement($resena)) {
            // set the owning side to null (unless already changed)
            if ($resena->getProducto() === $this) {
                $resena->setProducto(null);
            }
        }

        return $this;
    }
}
