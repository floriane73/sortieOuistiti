<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SortieRepository::class)
 * @ExclusionPolicy("all")
 */
class Sortie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups ({"sortie"})
     * @Expose
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="La sortie doit avoir un nom.")
     * @Groups ({"sortie"})
     * @Expose
     */
    private $nom;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThanOrEqual("today", message="La date de la sortie ne peut pas être dans le passé.")
     * @Groups ({"sortie"})
     * @Expose
     */
    private $dateHeureDebut;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Range(min="0", max="1000", notInRangeMessage="La sortie peut durer entre 0 et 1000 minutes.")
     * @Groups ({"sortie"})
     * @Expose
     */
    private $duree;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\LessThanOrEqual(propertyPath="dateHeureDebut", message="La clôture des inscriptions doit précéder le début de la sortie.")
     * @Groups ({"sortie"})
     * @Expose
     */
    private $dateLimiteInscription;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Range(min="0", max="200", notInRangeMessage="Il peut y avoir entre 0 et 200 places.")
     * @Groups ({"sortie"})
     * @Expose
     */
    private $nbInscriptionsMax;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups ({"sortie"})
     * @Expose
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Lieu::class, inversedBy="sorties", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     * @Groups ({"sortie"})
     * @Expose
     */
    private $lieu;

    /**
     * @ORM\ManyToOne(targetEntity=EtatSortie::class, inversedBy="sorties", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     * @Groups ({"sortie"})
     * @Expose
     */
    private $etatSortie;

    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="sorties", fetch="EAGER")
     * @Groups ({"sortie"})
     * @Expose
     */
    private $campus;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="sortiesOrganisees", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     * @Groups ({"sortie"})
     * @Expose
     */
    private $participantOrganisateur;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="sortiesChoisies", fetch="EAGER")
     * @Groups ({"sortie"})
     * @Expose
     */
    private $participantsInscrits;


    public function __construct()
    {
        $this->participantsInscrits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateHeureDebut(): ?\DateTimeInterface
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(\DateTimeInterface $dateHeureDebut): self
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateLimiteInscription(): ?\DateTimeInterface
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(?\DateTimeInterface $dateLimiteInscription): self
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getNbInscriptionsMax(): ?int
    {
        return $this->nbInscriptionsMax;
    }

    public function setNbInscriptionsMax(?int $nbInscriptionsMax): self
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getEtatSortie(): ?EtatSortie
    {
        return $this->etatSortie;
    }

    public function setEtatSortie(?EtatSortie $etatSortie): self
    {
        $this->etatSortie = $etatSortie;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    public function getParticipantOrganisateur(): ?User
    {
        return $this->participantOrganisateur;
    }

    public function setParticipantOrganisateur(?User $participantOrganisateur): self
    {
        $this->participantOrganisateur = $participantOrganisateur;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getParticipantsInscrits(): Collection
    {
        return $this->participantsInscrits;
    }

    public function addParticipantsInscrit(User $participantsInscrit): self
    {
        if (!$this->participantsInscrits->contains($participantsInscrit)) {
            $this->participantsInscrits[] = $participantsInscrit;
            $participantsInscrit->addSortiesChoisy($this);
        }

        return $this;
    }

    public function removeParticipantsInscrit(User $participantsInscrit): self
    {
        if ($this->participantsInscrits->removeElement($participantsInscrit)) {
            $participantsInscrit->removeSortiesChoisy($this);
        }

        return $this;
    }
}
