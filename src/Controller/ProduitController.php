<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{
    /**
     * @Route("/produit/{produit}", name="produit")
     */
    public function index(Produit $produit,CommentaireRepository $commentaireRepository,EntityManagerInterface $manager)
    {
        $commentaire=new Commentaire();
        $form=$this->createForm(CommentaireType::class,$commentaire);
        $message="";
        $formV=true;
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

        if($form->isSubmitted() && $form->isValid()){

            
            $this->addFlash(
                'success',
                'Vous avez bien commenté ce produit'
            );
            
            $manager->persist($commentaire);
            $manager->flush();
                
            return $this->redirectToRoute('produit', ['produit' => $produit->getId()]);      
        }
        
        
        return $this->render('produit/index.html.twig', [
            "produit"=>$produit,
            "myForm"=>$form->createView(),
            "formV"=>$formV,
            "message"=>$message
        ]);
    }
}
