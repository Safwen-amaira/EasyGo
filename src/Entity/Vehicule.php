<?php

namespace App\Entity;

use App\Repository\VehiculeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;


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
        max: 255,
        maxMessage: "Le nom du véhicule ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $name = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank(message: "La date de mise à jour est requise.")]
    private ?\DateTimeInterface $updated = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank(message: "La date de création est requise.")]
    private ?\DateTimeInterface $created = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $color = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: "Le prix du véhicule est requis.")]
    #[Assert\Positive(message: "Le prix doit être un nombre positif.")]
    private ?int $prix = null;

    #[ORM\Column(type: 'integer', name: 'total_en_stock')]
    #[Assert\NotBlank(message: "Le nombre total en stock est requis.")]
    #[Assert\Positive(message: "Le nombre total en stock doit être un nombre positif.")]
    private ?int $totalEnStock = null;

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

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(\DateTimeInterface $updated): self
    {
        $this->updated = $updated;
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

    public function getTotalEnStock(): ?int
    {
        return $this->totalEnStock;
    }

    public function setTotalEnStock(int $totalEnStock): self
    {
        $this->totalEnStock = $totalEnStock;
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