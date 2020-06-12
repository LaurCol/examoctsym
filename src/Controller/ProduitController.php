<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{
    /**
     * @Route("/produit/{produit}", name="produit")
     */
    public function index(Produit $produit,CommentaireRepository $commentaireRepository,EntityManagerInterface $manager,Request $request)
    {
        $commentaire=new Commentaire();
        $form=$this->createForm(CommentaireType::class,$commentaire);
        $message="";
        $formV=true;

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $commentaire->setUser($this->getUser())
                ->setProduit($produit);
            
            $this->addFlash(
                'success',
                'Vous avez bien commenté ce produit'
            );
            
            $manager->persist($commentaire);
            $manager->flush();
                
            return $this->redirectToRoute('produit', ['produit' => $produit->getId()]);      
        }

        if($this->getUser()==null)
        {
            $message="Vous devez être connecté pour commenter un produit";
            $formV=false;
        }
        else
        {
            $commentaire=$commentaireRepository->findOneBy(["produit"=>$produit,"user"=>$this->getUser()]);
            if($commentaire!=null)
            {
                $formV=false;
                $message="Vous avez déjà commenté ce produit";
            }
        }
        
        
        return $this->render('produit/index.html.twig', [
            "produit"=>$produit,
            "myForm"=>$form->createView(),
            "formV"=>$formV,
            "message"=>$message
        ]);
    }
}
