<?php

namespace App\Entity;

use App\Repository\UtilisateurfideliteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UtilisateurfideliteRepository::class)]
class Utilisateurfidelite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomUtilisateur = null;

    #[ORM\Column]
    private ?int $pointsAccumules = null;

    #[ORM\Column]
    private ?int $totalTrajetsEffectues = null;

    #[ORM\Column]
    private ?float $totalMontantDepense = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDerniereMiseAJour = null;

    /**
     * @var Collection<int, Recompensefidelite>
     */
    #[ORM\OneToMany(targetEntity: Recompensefidelite::class, mappedBy: 'Utilisateurfidelite')]
    private Collection $recompensefidelites;

    public function __construct()
    {
        $this->recompensefidelites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomUtilisateur(): ?string
    {
        return $this->nomUtilisateur;
    }

    public function setNomUtilisateur(string $nomUtilisateur): static
    {
        $this->nomUtilisateur = $nomUtilisateur;

        return $this;
    }

    public function getPointsAccumules(): ?int
    {
        return $this->pointsAccumules;
    }

    public function setPointsAccumules(int $pointsAccumules): static
    {
        $this->pointsAccumules = $pointsAccumules;

        return $this;
    }

    public function getTotalTrajetsEffectues(): ?int
    {
        return $this->totalTrajetsEffectues;
    }

    public function setTotalTrajetsEffectues(int $totalTrajetsEffectues): static
    {
        $this->totalTrajetsEffectues = $totalTrajetsEffectues;

        return $this;
    }

    public function getTotalMontantDepense(): ?float
    {
        return $this->totalMontantDepense;
    }

    public function setTotalMontantDepense(float $totalMontantDepense): static
    {
        $this->totalMontantDepense = $totalMontantDepense;

        return $this;
    }

    public function getDateDerniereMiseAJour(): ?\DateTimeInterface
    {
        return $this->dateDerniereMiseAJour;
    }

    public function setDateDerniereMiseAJour(\DateTimeInterface $dateDerniereMiseAJour): static
    {
        $this->dateDerniereMiseAJour = $dateDerniereMiseAJour;

        return $this;
    }

    /**
     * @return Collection<int, Recompensefidelite>
     */
    public function getRecompensefidelites(): Collection
    {
        return $this->recompensefidelites;
    }

    public function addRecompensefidelite(Recompensefidelite $recompensefidelite): static
    {
        if (!$this->recompensefidelites->contains($recompensefidelite)) {
            $this->recompensefidelites->add($recompensefidelite);
            $recompensefidelite->setUtilisateurfidelite($this);
        }

        return $this;
    }

    public function removeRecompensefidelite(Recompensefidelite $recompensefidelite): static
    {
        if ($this->recompensefidelites->removeElement($recompensefidelite)) {
            // set the owning side to null (unless already changed)
            if ($recompensefidelite->getUtilisateurfidelite() === $this) {
                $recompensefidelite->setUtilisateurfidelite(null);
            }
        }

        return $this;
    }
}
