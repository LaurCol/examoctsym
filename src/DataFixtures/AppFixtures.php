<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Entity\Commentaire;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    /**
     * encoder des mots de passe
     *
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passE)
    {
        $this->passwordEncoder=$passE;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $user = new User();
        $user->setEmail("colasse.la@gmail.com")
            ->setPassword($this->passwordEncoder->encodePassword($user,'password'))
            ->setRoles(["ROLE_ADMIN"])
            ;
        $manager->persist($user);

        $users = [];
        for ($i=0; $i < 10; $i++) { 

            $user = new User();
            $hash = $this->passwordEncoder->encodePassword($user, 'password');
            $user->setEmail($faker->email())
                ->setPassword($hash)
                ;
            $manager->persist($user);
            $users[] = $user;
        }


        $categories=["Instruments", "Accessoires", "CD","Vinyles"];
        $produits=[];
        for ($i=0; $i< 50; $i++)
        {
            $produit = new Produit();
            $produit->setNom($faker->words(mt_rand(1,3),true))
                ->setCategorie($faker->randomElement($categories))
                ->setPrix(mt_rand(100,20000)/100)
                ->setPhoto("default.png")
                ; 
            $manager->persist($produit);
            $produits[]=$produit;
        }
        $manager->flush();

        for($w = 1; $w < 20; $w++){
            $commande = new Commande();
            $panier=[];
            $total=0;
            $user = $faker->randomElement($users); 
            for ($j=0; $j < mt_rand(1,5); $j++) { 
                $prod=$produits[$j];
                $quantite=mt_rand(1,3);
                $panier[]=['quantite'=>$quantite,'produit'=>$prod->getId()];
                $total+=$quantite*$prod->getPrix();
            }
            
            $commande->setUser($user)
                ->setPanier($panier)
                ->setTotal($total);

            $manager->persist($commande);
        }

        foreach ($users as $user)
        {
            foreach($produits as $produit)
            {
                if(mt_rand(0,1))
                {
                    $commentaire = new Commentaire();
                    $commentaire->setUser($user)
                        ->setProduit($produit)
                        ->setMessage($faker->paragraph())
                        ->setNote(mt_rand(1,5))
                        ;
                    $manager->persist($commentaire);
                }
            }
        }

        $manager->flush();
    }
}
