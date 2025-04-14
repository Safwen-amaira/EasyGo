<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\RecompenseFideliteRepository;

#[ORM\Entity(repositoryClass: RecompenseFideliteRepository::class)]
#[ORM\Table(name: 'recompense_fidelite')]
class RecompenseFidelite
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

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $description = null;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $points_requis = null;

    public function getPoints_requis(): ?int
    {
        return $this->points_requis;
    }

    public function setPoints_requis(int $points_requis): self
    {
        $this->points_requis = $points_requis;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: TypeRecompense::class, inversedBy: 'recompenseFidelites')]
    #[ORM\JoinColumn(name: 'type_recompense_id', referencedColumnName: 'id')]
    private ?TypeRecompense $typeRecompense = null;

    public function getTypeRecompense(): ?TypeRecompense
    {
        return $this->typeRecompense;
    }

    public function setTypeRecompense(?TypeRecompense $typeRecompense): self
    {
        $this->typeRecompense = $typeRecompense;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Utilisateurfidelite::class, inversedBy: 'recompenseFidelites')]
    #[ORM\JoinColumn(name: 'utilisateurfidelite_id', referencedColumnName: 'id')]
    private ?Utilisateurfidelite $utilisateurfidelite = null;

    public function getUtilisateurfidelite(): ?Utilisateurfidelite
    {
        return $this->utilisateurfidelite;
    }

    public function setUtilisateurfidelite(?Utilisateurfidelite $utilisateurfidelite): self
    {
        $this->utilisateurfidelite = $utilisateurfidelite;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $statut = null;

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $date_expiration = null;

    public function getDate_expiration(): ?\DateTimeInterface
    {
        return $this->date_expiration;
    }

    public function setDate_expiration(?\DateTimeInterface $date_expiration): self
    {
        $this->date_expiration = $date_expiration;
        return $this;
    }

    public function getPointsRequis(): ?int
    {
        return $this->points_requis;
    }

    public function setPointsRequis(int $points_requis): static
    {
        $this->points_requis = $points_requis;

        return $this;
    }

    public function getDateExpiration(): ?\DateTimeInterface
    {
        return $this->date_expiration;
    }

    public function setDateExpiration(?\DateTimeInterface $date_expiration): static
    {
        $this->date_expiration = $date_expiration;

        return $this;
    }

}