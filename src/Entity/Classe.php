<?php

namespace App\Entity;

use App\Repository\ClasseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClasseRepository::class)]
class Classe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomClasse = null;

    #[ORM\ManyToOne(inversedBy: 'Session')]
    private ?Niveau $id_niveau = null;

    /**
     * @var Collection<int, Session>
     */
    #[ORM\OneToMany(targetEntity: Session::class, mappedBy: 'classe')]
    private Collection $Session;

    #[ORM\ManyToOne(inversedBy: 'classe')]
    private ?Niveau $niveau = null;

    public function __construct()
    {
        $this->Session = new ArrayCollection();
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

    public function getNomClasse(): ?string
    {
        return $this->nomClasse;
    }

    public function setNomClasse(string $nomClasse): static
    {
        $this->nomClasse = $nomClasse;

        return $this;
    }

    public function getNiveau(): ?string
    {
        return $this->Niveau;
    }

    public function setNiveau(string $Niveau): static
    {
        $this->Niveau = $Niveau;

        return $this;
    }

    public function getIdNiveau(): ?Niveau
    {
        return $this->id_niveau;
    }

    public function setIdNiveau(?Niveau $id_niveau): static
    {
        $this->id_niveau = $id_niveau;

        return $this;
    }

    /**
     * @return Collection<int, Session>
     */
    public function getSession(): Collection
    {
        return $this->Session;
    }

    public function addSession(Session $session): static
    {
        if (!$this->Session->contains($session)) {
            $this->Session->add($session);
            $session->setClasse($this);
        }

        return $this;
    }

    public function removeSession(Session $session): static
    {
        if ($this->Session->removeElement($session)) {
            // set the owning side to null (unless already changed)
            if ($session->getClasse() === $this) {
                $session->setClasse(null);
            }
        }

        return $this;
    }
}
