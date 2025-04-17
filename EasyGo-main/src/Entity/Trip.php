<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\TripRepository;

#[ORM\Entity(repositoryClass: TripRepository::class)]
class Trip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "La date du voyage est obligatoire")]
    private ?\DateTimeInterface $trip_date = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le point de départ est obligatoire")]
    #[Assert\Length(max: 255)]
    private ?string $departure_point = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La destination est obligatoire")]
    #[Assert\Length(max: 255)]
    private ?string $destination = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Assert\NotBlank(message: "L'heure de départ est obligatoire")]
    private ?\DateTimeInterface $departure_time = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $return_time = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le nombre de places est obligatoire")]
    #[Assert\Positive(message: "Le nombre de places doit être supérieur à 0")]
    private ?int $available_seats = null;

    #[ORM\Column(length: 20)]
    private ?string $trip_type = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Assert\PositiveOrZero(message: "La cotisation doit être positive ou nulle")]
    private ?string $contribution = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $relay_points = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTripDate(): ?\DateTimeInterface
    {
        return $this->trip_date;
    }

    public function setTripDate(\DateTimeInterface $trip_date): self
    {
        $this->trip_date = $trip_date;
        return $this;
    }

    public function getDeparturePoint(): ?string
    {
        return $this->departure_point;
    }

    public function setDeparturePoint(string $departure_point): self
    {
        $this->departure_point = $departure_point;
        return $this;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(string $destination): self
    {
        $this->destination = $destination;
        return $this;
    }

    public function getDepartureTime(): ?\DateTimeInterface
    {
        return $this->departure_time;
    }

    public function setDepartureTime(\DateTimeInterface $departure_time): self
    {
        $this->departure_time = $departure_time;
        return $this;
    }

    public function getReturnTime(): ?\DateTimeInterface
    {
        return $this->return_time;
    }

    public function setReturnTime(?\DateTimeInterface $return_time): self
    {
        $this->return_time = $return_time;
        return $this;
    }

    public function getAvailableSeats(): ?int
    {
        return $this->available_seats;
    }

    public function setAvailableSeats(int $available_seats): self
    {
        $this->available_seats = $available_seats;
        return $this;
    }

    public function getTripType(): ?string
    {
        return $this->trip_type;
    }

    public function setTripType(?string $trip_type): self
    {
        $this->trip_type = $trip_type;
        return $this;
    }

    public function getContribution(): ?string
    {
        return $this->contribution;
    }

    public function setContribution(?string $contribution): self
    {
        $this->contribution = $contribution;
        return $this;
    }

    public function getRelayPoints(): ?string
    {
        return $this->relay_points;
    }

    public function setRelayPoints(?string $relay_points): self
    {
        $this->relay_points = $relay_points;
        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setTrip($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getTrip() === $this) {
                $reservation->setTrip(null);
            }
        }

        return $this;
    }
    
}