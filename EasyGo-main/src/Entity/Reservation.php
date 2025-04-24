<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReservationRepository;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\Table(name: 'reservation')]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Trip::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(name: 'trip_id', referencedColumnName: 'id')]
    private ?Trip $trip = null;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTimeInterface $dateReservation = null;

    #[ORM\Column(type: 'string', length: 50, nullable: false)]
    private ?string $etatReservation = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: false)]
    private ?string $montantTotal = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $lieuEscale = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\NotBlank(message: "Le nombre de places est obligatoire")]
    #[Assert\Positive(message: "Le nombre de places doit être supérieur à 0")]
    #[Assert\Expression(
        "this.getNombrePlaces() <= this.getTrip().getAvailableSeats()",
        message: "Le nombre de places demandé dépasse les places disponibles"
    )]
    private ?int $nombrePlaces = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Assert\NotBlank(message: "Le type de handicap est obligatoire")]
    private ?string $typeHandicap = null;

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getTrip(): ?Trip
    {
        return $this->trip;
    }

    public function setTrip(?Trip $trip): self
    {
        $this->trip = $trip;
        return $this;
    }

    public function getDateReservation(): ?\DateTimeInterface
    {
        return $this->dateReservation;
    }

    public function setDateReservation(\DateTimeInterface $dateReservation): self
    {
        $this->dateReservation = $dateReservation;
        return $this;
    }

    public function getEtatReservation(): ?string
    {
        return $this->etatReservation;
    }

    public function setEtatReservation(string $etatReservation): self
    {
        $this->etatReservation = $etatReservation;
        return $this;
    }

    public function getMontantTotal(): ?string
    {
        return $this->montantTotal;
    }

    public function setMontantTotal(string $montantTotal): self
    {
        $this->montantTotal = $montantTotal;
        return $this;
    }

    public function getLieuEscale(): ?string
    {
        return $this->lieuEscale;
    }

    public function setLieuEscale(?string $lieuEscale): self
    {
        $this->lieuEscale = $lieuEscale;
        return $this;
    }

    public function getNombrePlaces(): ?int
    {
        return $this->nombrePlaces;
    }

    public function setNombrePlaces(?int $nombrePlaces): self
    {
        $this->nombrePlaces = $nombrePlaces;
        return $this;
    }

    public function getTypeHandicap(): ?string
    {
        return $this->typeHandicap;
    }

    public function setTypeHandicap(?string $typeHandicap): self
    {
        $this->typeHandicap = $typeHandicap;
        return $this;
    }
}