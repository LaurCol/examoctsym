<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\ProduitRepository;
use App\Services\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommandeController extends AbstractController
{
    /**
     * @Route("/admin/commandes/{page<\d+>?1}", name="admin_commandes_index")
     */
    public function index($page, PaginationService $pagination, EntityManagerInterface $manager)
    {
        $pagination->setEntityClass(Commande::class)
                ->setPage($page)
                ->setLimit(10)
                ;

        return $this->render('admin/commande/index.html.twig', [
           'pagination' => $pagination,
           "manager"=>$manager
        ]);
    }

    /**
     * Permet de supprimer une réservation
     * @Route("/admin/commandes/{id}/delete", name="admin_commandes_delete")
     */
    public function delete(Commande $commande, EntityManagerInterface $manager){
        $manager->remove($commande);
        $manager->flush();

        $this->addFlash(
            'success',
            "La commande a bien été supprimée"
        );

        return $this->redirectToRoute("admin_commandes_index");
    }
}