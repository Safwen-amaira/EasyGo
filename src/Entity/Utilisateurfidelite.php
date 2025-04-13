<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\UtilisateurfideliteRepository;

#[ORM\Entity(repositoryClass: UtilisateurfideliteRepository::class)]
#[ORM\Table(name: 'utilisateurfidelite')]
class Utilisateurfidelite
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

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $points_accumules = null;

    public function getPoints_accumules(): ?int
    {
        return $this->points_accumules;
    }

    public function setPoints_accumules(?int $points_accumules): self
    {
        $this->points_accumules = $points_accumules;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: RecompenseFidelite::class, mappedBy: 'utilisateurfidelite')]
    private Collection $recompenseFidelites;

    public function __construct()
    {
        $this->recompenseFidelites = new ArrayCollection();
    }

    /**
     * @return Collection<int, RecompenseFidelite>
     */
    public function getRecompenseFidelites(): Collection
    {
        if (!$this->recompenseFidelites instanceof Collection) {
            $this->recompenseFidelites = new ArrayCollection();
        }
        return $this->recompenseFidelites;
    }

    public function addRecompenseFidelite(RecompenseFidelite $recompenseFidelite): self
    {
        if (!$this->getRecompenseFidelites()->contains($recompenseFidelite)) {
            $this->getRecompenseFidelites()->add($recompenseFidelite);
        }
        return $this;
    }

    public function removeRecompenseFidelite(RecompenseFidelite $recompenseFidelite): self
    {
        $this->getRecompenseFidelites()->removeElement($recompenseFidelite);
        return $this;
    }

    public function getPointsAccumules(): ?int
    {
        return $this->points_accumules;
    }

    public function setPointsAccumules(?int $points_accumules): static
    {
        $this->points_accumules = $points_accumules;

        return $this;
    }



    #[ORM\Column(type: 'float', nullable: true)]
private ?float $totalMontant = 0;

#[ORM\Column(type: 'integer', nullable: true)]
private ?int $totalTrajets = 0;

// GETTERS & SETTERS

public function getTotalMontant(): ?float
{
    return $this->totalMontant;
}

public function setTotalMontant(?float $totalMontant): self
{
    $this->totalMontant = $totalMontant;
    return $this;
}

public function getTotalTrajets(): ?int
{
    return $this->totalTrajets;
}

public function setTotalTrajets(?int $totalTrajets): self
{
    $this->totalTrajets = $totalTrajets;
    return $this;
}
#[ORM\Column(type: 'string', length: 100, nullable: true)]
private ?string $nom_utilisateur = null;

public function getNomUtilisateur(): ?string
{
    return $this->nom_utilisateur;
}

public function setNomUtilisateur(?string $nom): self
{
    $this->nom_utilisateur = $nom;
    return $this;
}


}
