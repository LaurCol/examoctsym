<?php

namespace App\Controller;

use App\Services\StatsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard_index")
     */
    public function index(EntityManagerInterface $manager, StatsService $statsService)
    {
        $users = $statsService->getUsersCount();
        $produits = $statsService->getProduitsCount();
        $commandes = $statsService->getCommandesCount();
        $commentaires = $statsService->getCommentairesCount();
        return $this->render('admin/dashboard/index.html.twig', [
           'stats' => compact('users','produits','commandes','commentaires'),
        ]);
    }
}