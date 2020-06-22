<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Produit;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass=CommandeRepository::class)
 * @ApiResource(     
 *      normalizationContext={
 *          "groups"={"Commande_read"}
 *      }
 *  )
 */
class Commande
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"Commande_read"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="commandes")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"Commande_read"})
     */
    private $user;

    /**
     * @ORM\Column(type="json")
     * @Groups({"Commande_read"})
     */
    private $panier = [];

    /**
     * @ORM\Column(type="float")
     * @Groups({"Commande_read"})
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
