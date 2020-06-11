<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeController extends AbstractController
{
    /**
     * @Route("/commandes", name="commandes")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function index(EntityManagerInterface $manager)
    {
        return $this->render('commande/index.html.twig', [
            "manager"=>$manager
        ]);
    }

    /**
     * @Route("/commandes/{commande}", name="commande_view")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function view(Commande $commande,EntityManagerInterface $manager)
    {
        return $this->render("commande/view.html.twig",[
            "commande"=>$commande,
            "manager"=>$manager
        ]);
    }
}
