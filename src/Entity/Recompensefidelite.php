<?php

namespace App\Entity;

use App\Repository\RecompensefideliteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecompensefideliteRepository::class)]
class Recompensefidelite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $descriptionRecompense = null;

    #[ORM\Column]
    private ?int $pointsRequis = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $typeRecompense = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateExpiration = null;

    #[ORM\Column(nullable: true)]
    private ?int $bonusPoints = null;

    #[ORM\ManyToOne(inversedBy: 'recompensefidelites')]
    private ?Utilisateurfidelite $Utilisateurfidelite = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescriptionRecompense(): ?string
    {
        return $this->descriptionRecompense;
    }

    public function setDescriptionRecompense(?string $descriptionRecompense): static
    {
        $this->descriptionRecompense = $descriptionRecompense;

        return $this;
    }

    public function getPointsRequis(): ?int
    {
        return $this->pointsRequis;
    }

    public function setPointsRequis(int $pointsRequis): static
    {
        $this->pointsRequis = $pointsRequis;

        return $this;
    }

    public function getTypeRecompense(): ?string
    {
        return $this->typeRecompense;
    }

    public function setTypeRecompense(?string $typeRecompense): static
    {
        $this->typeRecompense = $typeRecompense;

        return $this;
    }

    public function getDateExpiration(): ?\DateTimeInterface
    {
        return $this->dateExpiration;
    }

    public function setDateExpiration(\DateTimeInterface $dateExpiration): static
    {
        $this->dateExpiration = $dateExpiration;

        return $this;
    }

    public function getBonusPoints(): ?int
    {
        return $this->bonusPoints;
    }

    public function setBonusPoints(?int $bonusPoints): static
    {
        $this->bonusPoints = $bonusPoints;

        return $this;
    }

    public function getUtilisateurfidelite(): ?Utilisateurfidelite
    {
        return $this->Utilisateurfidelite;
    }

    public function setUtilisateurfidelite(?Utilisateurfidelite $Utilisateurfidelite): static
    {
        $this->Utilisateurfidelite = $Utilisateurfidelite;

        return $this;
    }
}
