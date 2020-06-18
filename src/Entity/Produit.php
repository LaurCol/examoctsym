<?php

namespace App\Entity;

use App\Entity\Commentaire;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProduitRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass=ProduitRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @ApiResource(     
 *      normalizationContext={
 *          "groups"={"Produit_read"}
 *      }
 *  )
 * @ApiFilter(SearchFilter::class)
 * @ApiFilter(OrderFilter::class)
 */
class Produit
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"Produit_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez renseigner le nom du produit")
     * @Groups({"Produit_read", "Commentaire_read"})
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez renseigner la catégorie du produit")
     * @Assert\Choice({"Instruments", "Accessoires", "CD","Vinyles"})
     * @Groups({"Produit_read"})
     */
    private $categorie;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message="Vous devez renseigner le prix du produit")
     * @Assert\PositiveOrZero(message="Le prix doit être positif ou zero")
     * @Groups({"Produit_read"})
     */
    private $prix;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Image(mimeTypes={"image/png","image/jpeg","image/gif"}, mimeTypesMessage="Vous devez upload un fichier jpg, png ou gif", groups={"front"})
     * @Assert\File(maxSize="1024k", maxSizeMessage="taille du fichier trop grande", groups={"front"})
     * @Groups({"Produit_read"})
     */
    private $photo;

    /**
     * @ORM\OneToMany(targetEntity=Commentaire::class, mappedBy="produit", orphanRemoval=true)
     * @Groups({"Produit_read"})
     */
    private $commentaires;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"Produit_read"})
     */
    private $slug;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
    }

    /**
     * Permet d'intialiser le slug
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function initializeSlug(){
        $slugger = new AsciiSlugger();
        if(empty($this->slug)){
            $this->slug = $slugger->slug($this->nom);
        }
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

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return Collection|Commentaire[]
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setProduit($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->contains($commentaire)) {
            $this->commentaires->removeElement($commentaire);
            // set the owning side to null (unless already changed)
            if ($commentaire->getProduit() === $this) {
                $commentaire->setProduit(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getAvgNote()
    {
        $note=0;
        foreach ($this->getCommentaires() as $commentaire) {
            $note+=$commentaire->getNote();
        }
        $avgNote=$note/count($this->getCommentaires());
        return round($avgNote,1);
    }
}
