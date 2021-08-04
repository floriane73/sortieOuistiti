<?php

namespace App\Entity;

use App\Repository\EtatSortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=EtatSortieRepository::class)
 */
class EtatSortie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups ("etatSortie")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Groups ("etatSortie")
     */
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="etatSortie", fetch="LAZY")
     * @Groups ("etatSortie_details")
     */
    private $sorties;

    public function __construct()
    {
        $this->sorties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSorty(Sortie $sorty): self
    {
        if (!$this->sorties->contains($sorty)) {
            $this->sorties[] = $sorty;
            $sorty->setEtatSortie($this);
        }

        return $this;
    }

    public function removeSorty(Sortie $sorty): self
    {
        if ($this->sorties->removeElement($sorty)) {
            // set the owning side to null (unless already changed)
            if ($sorty->getEtatSortie() === $this) {
                $sorty->setEtatSortie(null);
            }
        }

        return $this;
    }
}
