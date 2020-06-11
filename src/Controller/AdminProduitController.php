<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Entity\ProduitPhotoEdit;
use App\Form\ProduitPhotoEditType;
use App\Services\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminProduitController extends AbstractController
{
    /**
     * @Route("/admin/produits/{page<\d+>?1}", name="admin_produits_index")
     */
    public function index($page, PaginationService $pagination) // $page = 1 plus obligatoire avec <\d+>?1 optionnel ? valeur 1
    {
        $pagination->setEntityClass(Produit::class)
                ->setPage($page)
                ->setLimit(10)
                ->setRoute('admin_produits_index');

      return $this->render('admin/produit/index.html.twig',[
          'pagination' => $pagination
      ]);
    }

    /**
     * Permet d'afficher le formulaire d'édition
     * @Route("/admin/produits/nouveau", name="admin_produits_nouveau")
     */
    public function new( Request $request, EntityManagerInterface $manager){
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class,$produit);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $file = $form['photoFile']->getData();
            if(!empty($file)){
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
                try{
                   $file->move(
                       $this->getParameter('uploads_directory'),
                       $newFilename
                   ); 
                }catch(FileException $e){
                    return $e->getMessage();
                }
                $produit->setPhoto($newFilename);
            }
            else
            {
                $produit->setPhoto('default.png');
            }
            
            $manager->persist($produit);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le produit <strong>{$produit->getNom()}</strong> a bien été créé"
            );
            return $this->redirectToRoute("admin_produits_index");
        }

        return $this->render('admin/produit/new.html.twig',[
            'produit' => $produit,
            'myForm' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'édition
     * @Route("/admin/produits/{id}/edit", name="admin_produits_edit")
     */
    public function edit(Produit $produit, Request $request, EntityManagerInterface $manager){
        $form = $this->createForm(ProduitType::class,$produit);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            $manager->persist($produit);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le produit <strong>{$produit->getNom()}</strong> a bien été modifié"
            );
        }

        return $this->render('admin/produit/edit.html.twig',[
            'produit' => $produit,
            'myForm' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'édition de la photo
     * @Route("/admin/produits/{produit}/edit/photo", name="admin_produits_edit_photo")
     */
    public function editPhoto(Produit $produit, Request $request, EntityManagerInterface $manager)
    {
        $photoEdit = new ProduitPhotoEdit();
        $form = $this->createForm(ProduitPhotoEditType::class,$photoEdit);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){   
            $file = $form['photoFile']->getData();
            if(!empty($file)){
                $photo=$produit->getPhoto();
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
                try{
                   $file->move(
                       $this->getParameter('uploads_directory'),
                       $newFilename
                   ); 
                }catch(FileException $e){
                    return $e->getMessage();
                }
                $produit->setPhoto($newFilename);
                if($photo!="default.png") unlink($this->getParameter("uploads_directory")."/".$photo);
            }

            $manager->persist($produit);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le produit <strong>{$produit->getNom()}</strong> a bien été modifié"
            );
            return $this->redirectToRoute("admin_produits_index");
        }

        return $this->render('admin/produit/editPhoto.html.twig',[
            'produit' => $produit,
            'myForm' => $form->createView()
        ]);
        
    }

    /**
     * Permet de supprimer une annonce
     * @Route("/admin/produits/{id}/delete", name="admin_produits_delete")
     */
    public function delete(Produit $produit, EntityManagerInterface $manager){
        $photo=$produit->getPhoto();
        if($photo!="default.png")
        {
            unlink($this->getParameter("uploads_directory")."/".$photo);
        }
        $manager->remove($produit);
        $manager->flush();
        $this->addFlash(
            'success',
            "Le produit <strong>{$produit->getNom()}</strong> et ses commentaires ont bien été supprimés"
        );
        return $this->redirectToRoute('admin_produits_index');
    }
}