<?php 

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;

class StatsService{

    private $manager;

    public function __construct(EntityManagerInterface $manager){
        $this->manager = $manager;
    }

    public function getUsersCount(){
        return $this->manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
    }

    public function getProduitsCount(){
        return $this->manager->createQuery('SELECT COUNT(p) FROM App\Entity\Produit p')->getSingleScalarResult();
    }

    public function getCommandesCount(){
        return $this->manager->createQuery('SELECT COUNT(c) FROM App\Entity\Commande c')->getSingleScalarResult();
    }

    public function getCommentairesCount(){
        return $this->manager->createQuery('SELECT COUNT(c) FROM App\Entity\Commentaire c')->getSingleScalarResult();
    }
}
