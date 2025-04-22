<?php

namespace App\Entity;

use App\Repository\ContratRepository;
use App\Entity\Vehicule;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ContratRepository::class)]
class Contrat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['contract:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Vehicule::class)]
    #[ORM\JoinColumn(name: 'idVehicule', referencedColumnName: 'id')]
    #[Groups(['contract:read'])]
    private ?Vehicule $vehicule = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom du contrat est requis.")]
    #[Assert\Length(
        min: 4,
        minMessage: "Le nom du contrat doit comporter au moins {{ limit }} caractères.",
        max: 255,
        maxMessage: "Le nom du contrat ne peut pas dépasser {{ limit }} caractères."
    )]
    #[Groups(['contract:read'])]
    private ?string $nomprenom = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['contract:read'])]
    private ?string $adresse = null;

    #[Assert\NotBlank(message: "Le numéro de téléphone est requis.")]
    #[Assert\Regex(
        pattern: "/^\+?[0-9]{8,15}$/",
        message: "Le numéro de téléphone est invalide."
    )]
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['contract:read'])]
    private ?string $telephone = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['contract:read'])]
    private ?string $typeContrat = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank(message: "La date de début est requise.")]
    #[Groups(['contract:read'])]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Groups(['contract:read'])]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['contract:read'])]
    private ?string $description = null;

    // Getters & Setters...

    public function getId(): ?int { return $this->id; }

    public function getVehicule(): ?Vehicule { return $this->vehicule; }

    public function setVehicule(?Vehicule $vehicule): self
    {
        $this->vehicule = $vehicule;
        return $this;
    }

    public function getNomprenom(): ?string { return $this->nomprenom; }

    public function setNomprenom(?string $nomprenom): self
    {
        $this->nomprenom = $nomprenom;
        return $this;
    }

    public function getAdresse(): ?string { return $this->adresse; }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getTelephone(): ?string { return $this->telephone; }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getTypeContrat(): ?string { return $this->typeContrat; }

    public function setTypeContrat(?string $typeContrat): self
    {
        $this->typeContrat = $typeContrat;
        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface { return $this->dateDebut; }

    public function setDateDebut(?\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface { return $this->dateFin; }

    public function setDateFin(?\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    public function getDescription(): ?string { return $this->description; }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }
}
