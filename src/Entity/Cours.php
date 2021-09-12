<?php

namespace App\Entity;

use App\Repository\CoursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CoursRepository::class)
 */
class Cours
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $populaire;

    /**
     * @ORM\OneToMany(targetEntity=Pdf::class, mappedBy="cours")
     */
    private $pdfs;

    /**
     * @ORM\ManyToMany(targetEntity=Etudiant::class, mappedBy="cours")
     */
    private $etudiants;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $frais;

    /**
     * @ORM\Column(type="integer")
     */
    private $placedisponible;

    /**
     * @ORM\Column(type="boolean")
     */
    private $gratuit;

    /**
     * @ORM\OneToMany(targetEntity=Video::class, mappedBy="cours")
     */
    private $videos;

    /**
     * @ORM\ManyToOne(targetEntity=Matiere::class, inversedBy="cours")
     */
    private $matiere;

    /**
     * @ORM\ManyToOne(targetEntity=Enseignant::class, inversedBy="cours")
     */
    private $enseignant;

    /**
     * @ORM\ManyToOne(targetEntity=Niveau::class, inversedBy="cours")
     */
    private $niveau;

    /**
     * @ORM\OneToMany(targetEntity=Groupe::class, mappedBy="cours")
     */
    private $groupes;

    public function __construct()
    {
        $this->pdfs = new ArrayCollection();
        $this->etudiants = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->groupes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage( $image): self
    {
        $this->image = $image;

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

  
    public function __toString() : string
    {
        return $this->getTitre();
    }

    public function getPopulaire(): ?bool
    {
        return $this->populaire;
    }

    public function setPopulaire(bool $populaire): self
    {
        $this->populaire = $populaire;

        return $this;
    }

    /**
     * @return Collection|Pdf[]
     */
    public function getPdfs(): Collection
    {
        return $this->pdfs;
    }

    public function addPdf(Pdf $pdf): self
    {
        if (!$this->pdfs->contains($pdf)) {
            $this->pdfs[] = $pdf;
            $pdf->setCours($this);
        }

        return $this;
    }

    public function removePdf(Pdf $pdf): self
    {
        if ($this->pdfs->removeElement($pdf)) {
            // set the owning side to null (unless already changed)
            if ($pdf->getCours() === $this) {
                $pdf->setCours(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Etudiant[]
     */
    public function getEtudiants(): Collection
    {
        return $this->etudiants;
    }

    public function addEtudiant(Etudiant $etudiant): self
    {
        if (!$this->etudiants->contains($etudiant)) {
            $this->etudiants[] = $etudiant;
            $etudiant->addCour($this);
        }

        return $this;
    }

    public function removeEtudiant(Etudiant $etudiant): self
    {
        if ($this->etudiants->removeElement($etudiant)) {
            $etudiant->removeCour($this);
        }

        return $this;
    }

    public function getFrais(): ?string
    {
        return $this->frais;
    }

    public function setFrais(string $frais): self
    {
        $this->frais = $frais;

        return $this;
    }

    public function getPlacedisponible(): ?int
    {
        return $this->placedisponible;
    }

    public function setPlacedisponible(int $placedisponible): self
    {
        $this->placedisponible = $placedisponible;

        return $this;
    }

    public function getGratuit(): ?bool
    {
        return $this->gratuit;
    }

    public function setGratuit(bool $gratuit): self
    {
        $this->gratuit = $gratuit;

        return $this;
    }

    /**
     * @return Collection|Video[]
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video): self
    {
        if (!$this->videos->contains($video)) {
            $this->videos[] = $video;
            $video->setCours($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): self
    {
        if ($this->videos->removeElement($video)) {
            // set the owning side to null (unless already changed)
            if ($video->getCours() === $this) {
                $video->setCours(null);
            }
        }

        return $this;
    }

    public function getMatiere(): ?Matiere
    {
        return $this->matiere;
    }

    public function setMatiere(?Matiere $matiere): self
    {
        $this->matiere = $matiere;

        return $this;
    }

    public function getEnseignant(): ?Enseignant
    {
        return $this->enseignant;
    }

    public function setEnseignant(?Enseignant $enseignant): self
    {
        $this->enseignant = $enseignant;

        return $this;
    }

    public function getNiveau(): ?Niveau
    {
        return $this->niveau;
    }

    public function setNiveau(?Niveau $niveau): self
    {
        $this->niveau = $niveau;

        return $this;
    }

    /**
     * @return Collection|Groupe[]
     */
    public function getGroupes(): Collection
    {
        return $this->groupes;
    }

    public function addGroupe(Groupe $groupe): self
    {
        if (!$this->groupes->contains($groupe)) {
            $this->groupes[] = $groupe;
            $groupe->setCours($this);
        }

        return $this;
    }

    public function removeGroupe(Groupe $groupe): self
    {
        if ($this->groupes->removeElement($groupe)) {
            // set the owning side to null (unless already changed)
            if ($groupe->getCours() === $this) {
                $groupe->setCours(null);
            }
        }

        return $this;
    }
}
