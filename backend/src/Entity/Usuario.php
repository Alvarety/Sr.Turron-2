<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    private ?string $Nombre = null;

    #[ORM\Column(length: 120)]
    private ?string $Apellido1 = null;

    #[ORM\Column(length: 120, nullable:true)]
    private ?string $Apellido2 = null;

    #[ORM\Column(length: 120)]
    private ?string $Nickname = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $contrasenaHash = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $telefono = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $direccion = null;

    #[ORM\Column(length: 20)]
    private ?string $rol = 'cliente';

    public const ROLES = ['cliente', 'admin', 'empleado'];

    #[ORM\Column(type:"datetime")]
    private ?\DateTimeInterface $fechaRegistro = null;

    #[ORM\Column(length: 255, nullable:true)]
    private ?string $fotoPerfil = null;

    /**
     * @var Collection<int, Pedido>
     */
    #[ORM\OneToMany(targetEntity: Pedido::class, mappedBy: 'usuario')]
    private Collection $pedidos;

    /**
     * @var Collection<int, Resena>
     */
    #[ORM\OneToMany(targetEntity: Resena::class, mappedBy: 'usuario')]
    private Collection $resenas;

    /**
     * @var Collection<int, Comentario>
     */
    #[ORM\OneToMany(targetEntity: Comentario::class, mappedBy: 'usuario')]
    private Collection $comentarios;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $passwordResetToken = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeInterface $passwordResetExpires = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resetToken = null;

    public function __construct()
    {
        $this->fechaRegistro = new \DateTime();
        $this->pedidos = new ArrayCollection();
        $this->resenas = new ArrayCollection();
        $this->comentarios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->Nombre;
    }

    public function setNombre(string $Nombre): static
    {
        $this->Nombre = $Nombre;

        return $this;
    }

    public function getApellido1(): ?string { return $this->Apellido1; }
    public function setApellido1(?string $Apellido1): static { $this->Apellido1 = $Apellido1; return $this; }

    public function getApellido2(): ?string { return $this->Apellido2; }
    public function setApellido2(?string $Apellido2): static { $this->Apellido2 = $Apellido2; return $this; }

    public function getNickname(): ?string { return $this->Nickname; }
    public function setNickname(?string $Nickname): static { $this->Nickname = $Nickname; return $this; }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getContrasenaHash(): ?string { return $this->contrasenaHash; }

    public function setContrasenaHash(string $contrasenaHash): static
    {
        $this->contrasenaHash = $contrasenaHash;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): static
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(?string $direccion): static
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getRol(): ?string
    {
        return $this->rol;
    }

    public function setRol(string $rol): self
    {
        if (!in_array($rol, self::ROLES)) {
            throw new \InvalidArgumentException("Rol inválido");
        }
        $this->rol = $rol;
        return $this;
    }

    public function getFechaRegistro(): ?\DateTimeInterface { return $this->fechaRegistro; }
    public function setFechaRegistro(\DateTimeInterface $fechaRegistro): self { $this->fechaRegistro = $fechaRegistro; return $this; }

    public function getFotoPerfil(): ?string { return $this->fotoPerfil; }
    public function setFotoPerfil(?string $fotoPerfil): static { $this->fotoPerfil = $fotoPerfil; return $this; }

    /**
     * @return Collection<int, Pedido>
     */
    public function getPedidos(): Collection
    {
        return $this->pedidos;
    }

    public function addPedido(Pedido $pedido): static
    {
        if (!$this->pedidos->contains($pedido)) {
            $this->pedidos->add($pedido);
            $pedido->setUsuario($this);
        }

        return $this;
    }

    public function removePedido(Pedido $pedido): static
    {
        if ($this->pedidos->removeElement($pedido)) {
            // set the owning side to null (unless already changed)
            if ($pedido->getUsuario() === $this) {
                $pedido->setUsuario(null);
            }
        }

        return $this;
    }

    public function getPassword(): string { return $this->contrasenaHash; }

    public function getUserIdentifier(): string
    {
        // Symfony usa esto en login → identificador único
        return $this->email;
    }

    public function getRoles(): array
    {
        return match ($this->rol) {
            'admin' => ['ROLE_ADMIN'],
            'empleado' => ['ROLE_EMPLEADO'],
            default => ['ROLE_USER'], // cliente u otro
        };
    }

    public function eraseCredentials(): void
    {
        // Si tuvieras datos sensibles temporales, los borras aquí.
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
            $resena->setUsuario($this);
        }

        return $this;
    }

    public function removeResena(Resena $resena): static
    {
        if ($this->resenas->removeElement($resena)) {
            // set the owning side to null (unless already changed)
            if ($resena->getUsuario() === $this) {
                $resena->setUsuario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comentario>
     */
    public function getComentarios(): Collection
    {
        return $this->comentarios;
    }

    public function addComentario(Comentario $comentario): static
    {
        if (!$this->comentarios->contains($comentario)) {
            $this->comentarios->add($comentario);
            $comentario->setUsuario($this);
        }

        return $this;
    }

    public function removeComentario(Comentario $comentario): static
    {
        if ($this->comentarios->removeElement($comentario)) {
            // set the owning side to null (unless already changed)
            if ($comentario->getUsuario() === $this) {
                $comentario->setUsuario(null);
            }
        }

        return $this;
    }

    public function getPasswordResetToken(): ?string
    {
        return $this->passwordResetToken;
    }

    public function setPasswordResetToken(?string $token): self
    {
        $this->passwordResetToken = $token;
        return $this;
    }

    public function getPasswordResetExpires(): ?\DateTimeInterface
    {
        return $this->passwordResetExpires;
    }

    public function setPasswordResetExpires(?\DateTimeInterface $expires): self
    {
        $this->passwordResetExpires = $expires;
        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;
        return $this;
    }
}
