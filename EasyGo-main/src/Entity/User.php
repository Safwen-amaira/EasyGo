<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\UserRepository;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;
    #[ORM\Column(type: 'string', length: 20, nullable: true)]
private ?string $phoneNumber = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $nom = null;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $prenom = null;

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $email = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $password = null;

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $cin = null;

    public function getCin(): ?string
    {
        return $this->cin;
    }

    public function setCin(string $cin): self
    {
        $this->cin = $cin;
        return $this;
    }

    #[ORM\Column(type: 'boolean', nullable: false)]
    private ?bool $is_rider = null;

    public function is_rider(): ?bool
    {
        return $this->is_rider;
    }

    public function setIs_rider(bool $is_rider): self
    {
        $this->is_rider = $is_rider;
        return $this;
    }

    #[ORM\Column(type: 'boolean', nullable: false)]
    private ?bool $is_admin = null;

    public function is_admin(): ?bool
    {
        return $this->is_admin;
    }

    public function setIs_admin(bool $is_admin): self
    {
        $this->is_admin = $is_admin;
        return $this;
    }

    #[ORM\Column(type: 'boolean', nullable: false)]
    private ?bool $is_driver = null;

    public function is_driver(): ?bool
    {
        return $this->is_driver;
    }

    public function setIs_driver(bool $is_driver): self
    {
        $this->is_driver = $is_driver;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $totp_secret = null;

    public function getTotp_secret(): ?string
    {
        return $this->totp_secret;
    }

    public function setTotp_secret(?string $totp_secret): self
    {
        $this->totp_secret = $totp_secret;
        return $this;
    }

    #[ORM\Column(type: 'boolean', nullable: false)]
    private ?bool $is_2fa_enabled = null;

    public function is_2fa_enabled(): ?bool
    {
        return $this->is_2fa_enabled;
    }

    public function setIs_2fa_enabled(bool $is_2fa_enabled): self
    {
        $this->is_2fa_enabled = $is_2fa_enabled;
        return $this;
    }

    public function getPhoneNumber(): ?string
{
    return $this->phoneNumber;
}

public function setPhoneNumber(?string $phoneNumber): self
{
    $this->phoneNumber = $phoneNumber;
    return $this;
}
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'user')]
    private Collection $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        if (!$this->reservations instanceof Collection) {
            $this->reservations = new ArrayCollection();
        }
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->getReservations()->contains($reservation)) {
            $this->getReservations()->add($reservation);
        }
        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        $this->getReservations()->removeElement($reservation);
        return $this;
    }

    public function isRider(): ?bool
    {
        return $this->is_rider;
    }

    public function setIsRider(bool $is_rider): static
    {
        $this->is_rider = $is_rider;

        return $this;
    }

    public function isAdmin(): ?bool
    {
        return $this->is_admin;
    }

    public function setIsAdmin(bool $is_admin): static
    {
        $this->is_admin = $is_admin;

        return $this;
    }

    public function isDriver(): ?bool
    {
        return $this->is_driver;
    }

    public function setIsDriver(bool $is_driver): static
    {
        $this->is_driver = $is_driver;

        return $this;
    }

    public function getTotpSecret(): ?string
    {
        return $this->totp_secret;
    }

    public function setTotpSecret(?string $totp_secret): static
    {
        $this->totp_secret = $totp_secret;

        return $this;
    }

    public function is2faEnabled(): ?bool
    {
        return $this->is_2fa_enabled;
    }

    public function setIs2faEnabled(bool $is_2fa_enabled): static
    {
        $this->is_2fa_enabled = $is_2fa_enabled;

        return $this;
    }

}