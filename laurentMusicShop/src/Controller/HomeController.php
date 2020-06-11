<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(ProduitRepository $produitRepository)
    {
        $produits=$produitRepository->findAll();
        $categorie="Tous les produits";
        return $this->render("index.html.twig",[
            "produits"=>$produits,
            "title"=>$categorie
        ]);
    }

    /**
     * @Route("/categorie/{categorie}",name="categorie")
     */
    public function categorie(String $categorie,ProduitRepository $produitRepository)
    {
        $produits=$produitRepository->findBy(["categorie"=>$categorie]);
        return $this->render("index.html.twig",[
            "produits"=>$produits,
            "title"=>$categorie
        ]);
    }
}
