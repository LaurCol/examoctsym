<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminUserType;
use App\Repository\RoleRepository;
use App\Services\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    /**
     * @Route("/admin/users/{page<\d+>?1}", name="admin_users_index")
     */
    public function index($page, PaginationService $pagination)
    {
        $pagination->setEntityClass(User::class)
                ->setPage($page)
                ->setLimit(10)
                ;
        return $this->render('admin/user/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet de supprimer un utilisateur
     * @Route("/admin/users/{id}/delete", name="admin_users_delete")
     */
    public function delete(User $user, EntityManagerInterface $manager){
        if(count($user->getCommandes()) > 0){
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas supprimer l'utilisateur <strong>{$user->getEmail()}</strong> car il possède des commandes"
            );
        }else{
            $manager->remove($user);
            $this->addFlash(
                'success',
                "L'utilisateur <strong>{$user->getEmail()}</strong> a bien été supprimé"
            );
            $manager->flush();
        }

        return $this->redirectToRoute('admin_users_index');
    }

    /**
     * Permet d'ajouter le rôle admin à un utilisateur
     * @Route("/admin/users/{id}/addAdmin", name="admin_users_add_admin")
     */
    public function addAdmin(User $user, EntityManagerInterface $manager){
        $roles=$user->getRoles();
        $roles[]="ROLE_ADMIN";
        $user->setRoles($roles);
        $manager->persist($user);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le rôle administrateur a bien été ajouté"
        );
        return $this->redirectToRoute('admin_users_index');
    }


    /**
     * Permet de supprimer le rôle admin à un membre
     * @Route("/admin/users/{id}/delAdmin", name="admin_users_del_admin")
     */
    public function removeAdmin(User $user, EntityManagerInterface $manager){
        $roles=$user->getRoles();
        $key=array_search("ROLE_ADMIN",$roles);
        array_splice($roles,$key,1);
        $user->setRoles($roles);
        $manager->persist($user);
        $manager->flush();

        $this->addFlash(
            'warning',
            "Le rôle administrateur a bien été supprimé"
        );
        return $this->redirectToRoute('admin_users_index');
    }



}