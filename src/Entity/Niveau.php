<?php

namespace App\Entity;

use App\Repository\NiveauRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NiveauRepository::class)]
class Niveau
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomNiveau = null;

    /**
     * @var Collection<int, Classe>
     */
    #[ORM\OneToMany(targetEntity: Classe::class, mappedBy: 'niveau')]
    private Collection $classe;

    public function __construct()
    {
        $this->Session = new ArrayCollection();
        $this->classe = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getNomNiveau(): ?string
    {
        return $this->nomNiveau;
    }

    public function setNomNiveau(string $nomNiveau): static
    {
        $this->nomNiveau = $nomNiveau;

        return $this;
    }

    /**
     * @return Collection<int, Classe>
     */
    public function getSession(): Collection
    {
        return $this->Session;
    }

    public function addSession(Classe $session): static
    {
        if (!$this->Session->contains($session)) {
            $this->Session->add($session);
            $session->setIdNiveau($this);
        }

        return $this;
    }

    public function removeSession(Classe $session): static
    {
        if ($this->Session->removeElement($session)) {
            // set the owning side to null (unless already changed)
            if ($session->getIdNiveau() === $this) {
                $session->setIdNiveau(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Classe>
     */
    public function getClasse(): Collection
    {
        return $this->classe;
    }

    public function addClasse(Classe $classe): static
    {
        if (!$this->classe->contains($classe)) {
            $this->classe->add($classe);
            $classe->setNiveau($this);
        }

        return $this;
    }

    public function removeClasse(Classe $classe): static
    {
        if ($this->classe->removeElement($classe)) {
            // set the owning side to null (unless already changed)
            if ($classe->getNiveau() === $this) {
                $classe->setNiveau(null);
            }
        }

        return $this;
    }
}
