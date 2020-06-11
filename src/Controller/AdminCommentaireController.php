<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Services\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentaireController extends AbstractController
{
    /**
     * @Route("/admin/commentaires/{page<\d+>?1}", name="admin_commentaires_index")
     */
    public function index($page, PaginationService $pagination)
    {
        $pagination->setEntityClass(Commentaire::class)
                ->setPage($page)
                ->setLimit(10)
                ;  
        return $this->render('admin/commentaire/index.html.twig', [
           'pagination' => $pagination
        ]);
    }

    /**
     * Permet de modifier un commentaire
     * @Route("/admin/commentaires/{id}/edit", name="admin_commentaires_edit")
     */
    public function edit(Commentaire $commentaire, Request $request, EntityManagerInterface $manager){
        $form = $this->createForm(CommentaireType::class, $commentaire);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($commentaire);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le commentaire n°<strong>{$commentaire->getId()}</strong> a été modifié"
            );
        }

        return $this->render('admin/commentaire/edit.html.twig',[
            'commentaire' => $commentaire,
            'myForm' => $form->createView()
        ]);

    }

    /**
     * Permet de supprimer un commentaire
     * @Route("/admin/commentaires/{id}/delete", name="admin_commentaires_delete")
     */
    public function delete(Commentaire $commentaire, EntityManagerInterface $manager){
        
        $manager->remove($commentaire);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le commentaire a bien été supprimée"
        );

        return $this->redirectToRoute('admin_commentaires_index');
    }
}