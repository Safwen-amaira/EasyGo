<?php

namespace App\Entity;

use App\Repository\VehiculeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\Date;


#[ORM\Entity(repositoryClass: VehiculeRepository::class)]
class Vehicule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom du véhicule est requis.")]
    #[Assert\Length(
        min: 4,
        minMessage: "Le nom du véhicule doit comporter au moins {{ limit }} caractères.",
        max: 8,
        maxMessage: "Le nom du véhicule ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $name = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: "Le type de carburant est requis..")]
    private ?string $Carburant = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank(message: "La date de création est requise.")]
    #[Assert\LessThan(
        value: "today",
        message: "La date de création ne peut pas être aujourd'hui."
    )]
    private ?\DateTimeInterface $created = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        min: 4,
        minMessage: "Le nom du véhicule doit comporter au moins {{ limit }} caractères.",
    )]
    private ?string $content = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 8,
        maxMessage: "La couleur ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $color = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: "Le prix du véhicule est requis.")]
    #[Assert\Positive(message: "Le prix doit être un nombre positif.")]
    private ?int $prix = null;

    #[ORM\Column(type: 'integer', name: 'NombrePlaces')]
    #[Assert\NotBlank(message: "Le Nombre Places en stock est requis.")]
    #[Assert\Positive(message: "Le Nombre Places en stock doit être un nombre positif.")]
    private ?int $NombrePlaces = null;

    #[ORM\Column(type: 'integer', name: 'categories_id')]
    #[Assert\NotBlank(message: "La catégorie du véhicule est requise.")]
    private ?int $categoriesId = null;

    // Getters and setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getCarburant(): ?string
    {
        return $this->Carburant;
    }

    public function setCarburant(string $Carburant): self
    {
        $this->Carburant = $Carburant;
        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;
        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    public function getNombrePlaces(): ?int
    {
        return $this->NombrePlaces;
    }

    public function setNombrePlaces(int $NombrePlaces): self
    {
        $this->NombrePlaces = $NombrePlaces;
        return $this;
    }

    public function getCategoriesId(): ?int
    {
        return $this->categoriesId;
    }

    public function setCategoriesId(int $categoriesId): self
    {
        $this->categoriesId = $categoriesId;
        return $this;
    }
}