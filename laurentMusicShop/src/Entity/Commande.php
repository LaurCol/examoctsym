<?php

namespace App\Entity;

use App\Entity\Produit;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @ORM\Entity(repositoryClass=CommandeRepository::class)
 */
class Commande
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="commandes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="json")
     */
    private $panier = [];

    /**
     * @ORM\Column(type="float")
     */
    private $total;

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

    public function getPanier(): ?array
    {
        return $this->panier;
    }

    public function setPanier(array $panier): self
    {
        $this->panier = $panier;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getPanierTab(EntityManagerInterface $manager)
    {
        $produitsRepository=$manager->getRepository(Produit::class);
        $panier2=[];
        foreach ($this->panier as $produit) {
            $id=$produit['produit'];
            $prod=$produitsRepository->findOneBy(["id"=>$id]);
            $panier2[]=[
                'produit'=>$prod,
                'quantite'=>$produit['quantite']
            ];
        }
        return $panier2;
    }
}