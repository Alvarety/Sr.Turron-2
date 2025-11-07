<?php

namespace App\Entity;

use App\Repository\PedidoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PedidoRepository::class)]
class Pedido
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'pedidos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $usuario = null;

    #[ORM\Column(type:"datetime")]
    private ?\DateTimeInterface $fechapedido = null;

    #[ORM\Column(length: 255)]
    private ?string $estado = 'pendiente';

    public const ESTADOS = ['pendiente','pagado','enviado','entregado','cancelado'];

    #[ORM\Column(type:"decimal", precision:10, scale:2)]
    private ?float $total = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $direccionEnvio = null;

    /**
     * @var Collection<int, Pago>
     */
    #[ORM\OneToMany(targetEntity: Pago::class, mappedBy: 'pedido')]
    private Collection $pagos;

    /**
     * @var Collection<int, PedidoDetalle>
     */
    #[ORM\OneToMany(targetEntity: PedidoDetalle::class, mappedBy: 'pedido')]
    private Collection $pedidoDetalles;

    public function __construct()
    {
        $this->fechapedido = new \DateTime();
        $this->pagos = new ArrayCollection();
        $this->pedidoDetalles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): static
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getFechapedido(): ?\DateTimeInterface
    {
        return $this->fechapedido;
    }

    public function setFechapedido(\DateTimeInterface $fechapedido): static
    {
        $this->fechapedido = $fechapedido;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): self
    {
        if (!in_array($estado, self::ESTADOS)) {
            throw new \InvalidArgumentException("Estado inválido");
        }
        $this->estado = $estado;
        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getDireccionEnvio(): ?string
    {
        return $this->direccionEnvio;
    }

    public function setDireccionEnvio(?string $direccionEnvio): static
    {
        $this->direccionEnvio = $direccionEnvio;

        return $this;
    }

    /**
     * @return Collection<int, Pago>
     */
    public function getPagos(): Collection
    {
        return $this->pagos;
    }

    public function addPago(Pago $pago): static
    {
        if (!$this->pagos->contains($pago)) {
            $this->pagos->add($pago);
            $pago->setPedido($this);
        }

        return $this;
    }

    public function removePago(Pago $pago): static
    {
        if ($this->pagos->removeElement($pago)) {
            // set the owning side to null (unless already changed)
            if ($pago->getPedido() === $this) {
                $pago->setPedido(null);
            }
        }

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
            $pedidoDetalle->setPedido($this);
        }

        return $this;
    }

    public function removePedidoDetalle(PedidoDetalle $pedidoDetalle): static
    {
        if ($this->pedidoDetalles->removeElement($pedidoDetalle)) {
            // set the owning side to null (unless already changed)
            if ($pedidoDetalle->getPedido() === $this) {
                $pedidoDetalle->setPedido(null);
            }
        }

        return $this;
    }
}
