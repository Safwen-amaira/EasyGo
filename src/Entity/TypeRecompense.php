<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\TypeRecompenseRepository;

#[ORM\Entity(repositoryClass: TypeRecompenseRepository::class)]
#[ORM\Table(name: 'type_recompense')]
class TypeRecompense
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
    private ?string $nom = null;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $categorie = null;

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(?string $categorie): self
    {
        $this->categorie = $categorie;
        return $this;
    }

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $actif = null;

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(?bool $actif): self
    {
        $this->actif = $actif;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: RecompenseFidelite::class, mappedBy: 'typeRecompense')]
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

}
