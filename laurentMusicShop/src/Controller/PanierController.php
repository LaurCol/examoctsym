<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Commande;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier",name="panier")
     */
    public function index(Session $session,ProduitRepository $produitRepository)
    {
        $panier=$session->get("panier",[]);
        $panier2=[];
        $total=0;
        foreach ($panier as $id => $quantite) {
            $produit=$produitRepository->findOneBy(["id"=>$id]);
            $panier2[]= [
                'produit'=>$produit,
                'quantite'=>$quantite
            ];
            $total+=($produit->getPrix()*$quantite);
        }
        return $this->render("account/panier.html.twig",[
            "panier"=>$panier2,
            "total"=>$total
        ]);
    }

    /**
     * @Route("/panier/commander",name="panier_commander")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function commander(Session $session,ProduitRepository $produitRepository, EntityManagerInterface $manager)
    {
        $panier=$session->get("panier",[]);
        if(empty($panier))
        {
            $this->addFlash(
                'error',
                'Vous ne pouvez pas commander un panier vide'
            );
        }
        $panier2=[];
        $total=0;
        foreach ($panier as $id => $quantite) {
            $produit=$produitRepository->find($id);
            $panier2[]= [
                'produit'=>$produit->getId(),
                'quantite'=>$quantite
            ];
            $total+=($produit->getPrix()*$quantite);
        }
        $commande=new Commande();

        $commande->setPanier($panier2)
            ->setTotal($total)
            ->setUser($this->getUser())
            ;
        $manager->persist($commande);
        $manager->flush();
        $session->set("panier",[]);

        $this->addFlash(
            'success',
            'Votre commande a bien été effectuée, un administrateur vous contactera par mail'
        );

        return $this->redirectToRoute("commande_view",["commande"=>$commande->getId()]);
    }

    /**
     * @Route("/panier/ajouter/{produit}",name="panier_ajouter")
     */
    public function ajouter(Produit $produit,Session $session,Request $request)
    {
        $panier=$session->get("panier",[]);
        if(isset($panier[$produit->getId()]))
        {
            $panier[$produit->getId()]++;
        }
        else
        {
            $panier[$produit->getId()]=1;
        }
        $session->set("panier",$panier);
        $this->addFlash(
            'success',
            'Une unité de '.$produit->getNom().' a été ajoutée à votre panier'
        );
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/panier/retirer/{produit}",name="panier_retirer")
     */
    public function retirer(Produit $produit,Session $session,Request $request)
    {
        $panier=$session->get("panier",[]);
        if(isset($panier[$produit->getId()]))
        {
            unset($panier[$produit->getId()]);
        }
        $session->set("panier",$panier);
        $this->addFlash(
            'success',
            'Vous avez bien enlevé '.$produit->getNom().' de votre panier'
        );
        return $this->redirect($request->headers->get('referer'));
    }

}