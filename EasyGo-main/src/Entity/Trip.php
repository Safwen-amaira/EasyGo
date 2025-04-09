<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\TripRepository;

#[ORM\Entity(repositoryClass: TripRepository::class)]
#[ORM\Table(name: 'trip')]
class Trip
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

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $trip_date = null;

    public function getTrip_date(): ?\DateTimeInterface
    {
        return $this->trip_date;
    }

    public function setTrip_date(?\DateTimeInterface $trip_date): self
    {
        $this->trip_date = $trip_date;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $departure_point = null;

    public function getDeparture_point(): ?string
    {
        return $this->departure_point;
    }

    public function setDeparture_point(string $departure_point): self
    {
        $this->departure_point = $departure_point;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $destination = null;

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(string $destination): self
    {
        $this->destination = $destination;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTimeInterface $departure_time = null;

    public function getDeparture_time(): ?\DateTimeInterface
    {
        return $this->departure_time;
    }

    public function setDeparture_time(\DateTimeInterface $departure_time): self
    {
        $this->departure_time = $departure_time;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $return_time = null;

    public function getReturn_time(): ?\DateTimeInterface
    {
        return $this->return_time;
    }

    public function setReturn_time(?\DateTimeInterface $return_time): self
    {
        $this->return_time = $return_time;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $available_seats = null;

    public function getAvailable_seats(): ?int
    {
        return $this->available_seats;
    }

    public function setAvailable_seats(int $available_seats): self
    {
        $this->available_seats = $available_seats;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $trip_type = null;

    public function getTrip_type(): ?string
    {
        return $this->trip_type;
    }

    public function setTrip_type(?string $trip_type): self
    {
        $this->trip_type = $trip_type;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: true)]
    private ?float $contribution = null;

    public function getContribution(): ?float
    {
        return $this->contribution;
    }

    public function setContribution(?float $contribution): self
    {
        $this->contribution = $contribution;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $relay_points = null;

    public function getRelay_points(): ?string
    {
        return $this->relay_points;
    }

    public function setRelay_points(?string $relay_points): self
    {
        $this->relay_points = $relay_points;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'trip')]
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

    public function getTripDate(): ?\DateTimeInterface
    {
        return $this->trip_date;
    }

    public function setTripDate(?\DateTimeInterface $trip_date): static
    {
        $this->trip_date = $trip_date;

        return $this;
    }

    public function getDeparturePoint(): ?string
    {
        return $this->departure_point;
    }

    public function setDeparturePoint(string $departure_point): static
    {
        $this->departure_point = $departure_point;

        return $this;
    }

    public function getDepartureTime(): ?\DateTimeInterface
    {
        return $this->departure_time;
    }

    public function setDepartureTime(\DateTimeInterface $departure_time): static
    {
        $this->departure_time = $departure_time;

        return $this;
    }

    public function getReturnTime(): ?\DateTimeInterface
    {
        return $this->return_time;
    }

    public function setReturnTime(?\DateTimeInterface $return_time): static
    {
        $this->return_time = $return_time;

        return $this;
    }

    public function getAvailableSeats(): ?int
    {
        return $this->available_seats;
    }

    public function setAvailableSeats(int $available_seats): static
    {
        $this->available_seats = $available_seats;

        return $this;
    }

    public function getTripType(): ?string
    {
        return $this->trip_type;
    }

    public function setTripType(?string $trip_type): static
    {
        $this->trip_type = $trip_type;

        return $this;
    }

    public function getRelayPoints(): ?string
    {
        return $this->relay_points;
    }

    public function setRelayPoints(?string $relay_points): static
    {
        $this->relay_points = $relay_points;

        return $this;
    }

}
