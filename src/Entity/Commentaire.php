<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommentaireRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass=CommentaireRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @ApiResource(     
 *      normalizationContext={
 *          "groups"={"Commentaire_read"}
 *      }
 *  )
 * @ApiFilter(SearchFilter::class)
 * @ApiFilter(OrderFilter::class)
 */
class Commentaire
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"Commentaire_read", "Produit_read"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="commentaires")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank
     * @Groups({"Commentaire_read", "Produit_read"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Produit::class, inversedBy="commentaires")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank
     * @Groups({"Commentaire_read"})
     */
    private $produit;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Vous devez entrer votre commentaire")
     * @Groups({"Commentaire_read", "User_read", "Produit_read"})
     */
    private $message;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Vous devez entrer votre note")
     * @Assert\Range(min = 0,max = 5,notInRangeMessage = "Votre note doit être entre 0 et 5")
     * @Groups({"Commentaire_read", "Produit_read"})
     */
    private $note;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     * @Groups({"Commentaire_read", "Produit_read"})
     */
    private $date;

    /**
     * Permet de mettre en place la date de création
     * @ORM\PrePersist
     */
    public function prePersist(){
        if(empty($this->date)){
            $this->date = new \DateTime();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
