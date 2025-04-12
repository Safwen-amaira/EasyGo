<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\ReservationRepository;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\Table(name: 'reservation')]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private ?User $user = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Trip::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(name: 'trip_id', referencedColumnName: 'id')]
    private ?Trip $trip = null;

    public function getTrip(): ?Trip
    {
        return $this->trip;
    }

    public function setTrip(?Trip $trip): self
    {
        $this->trip = $trip;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTimeInterface $date_reservation = null;

    public function getDate_reservation(): ?\DateTimeInterface
    {
        return $this->date_reservation;
    }

    public function setDate_reservation(\DateTimeInterface $date_reservation): self
    {
        $this->date_reservation = $date_reservation;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $etat_reservation = null;

    public function getEtat_reservation(): ?string
    {
        return $this->etat_reservation;
    }

    public function setEtat_reservation(string $etat_reservation): self
    {
        $this->etat_reservation = $etat_reservation;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: false)]
    private ?float $montant_total = null;

    public function getMontant_total(): ?float
    {
        return $this->montant_total;
    }

    public function setMontant_total(float $montant_total): self
    {
        $this->montant_total = $montant_total;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $lieu_escale = null;

    public function getLieu_escale(): ?string
    {
        return $this->lieu_escale;
    }

    public function setLieu_escale(?string $lieu_escale): self
    {
        $this->lieu_escale = $lieu_escale;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $nombre_places = null;

    public function getNombre_places(): ?int
    {
        return $this->nombre_places;
    }

    public function setNombre_places(?int $nombre_places): self
    {
        $this->nombre_places = $nombre_places;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $type_handicap = null;

    public function getType_handicap(): ?string
    {
        return $this->type_handicap;
    }

    public function setType_handicap(?string $type_handicap): self
    {
        $this->type_handicap = $type_handicap;
        return $this;
    }

    public function getDateReservation(): ?\DateTimeInterface
    {
        return $this->date_reservation;
    }

    public function setDateReservation(\DateTimeInterface $date_reservation): static
    {
        $this->date_reservation = $date_reservation;

        return $this;
    }

    public function getEtatReservation(): ?string
    {
        return $this->etat_reservation;
    }

    public function setEtatReservation(string $etat_reservation): static
    {
        $this->etat_reservation = $etat_reservation;

        return $this;
    }

    public function getMontantTotal(): ?string
    {
        return $this->montant_total;
    }

    public function setMontantTotal(string $montant_total): static
    {
        $this->montant_total = $montant_total;

        return $this;
    }

    public function getLieuEscale(): ?string
    {
        return $this->lieu_escale;
    }

    public function setLieuEscale(?string $lieu_escale): static
    {
        $this->lieu_escale = $lieu_escale;

        return $this;
    }

    public function getNombrePlaces(): ?int
    {
        return $this->nombre_places;
    }

    public function setNombrePlaces(?int $nombre_places): static
    {
        $this->nombre_places = $nombre_places;

        return $this;
    }

    public function getTypeHandicap(): ?string
    {
        return $this->type_handicap;
    }

    public function setTypeHandicap(?string $type_handicap): static
    {
        $this->type_handicap = $type_handicap;

        return $this;
    }

}
