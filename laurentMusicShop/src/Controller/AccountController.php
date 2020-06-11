<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\ModificationEmailType;
use Symfony\Component\Form\FormError;
use App\Form\ModificationPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/login", name="account_login")
     */
    public function index(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        return $this->render('account/login.html.twig', [
            'hasError' => $error !==null,
            'username' => $username
        ]);
    }

    /**
     * Permet de se déconnecter
     * @Route("/logout", name="account_logout")
     */
    public function logout(){
        //...
    }

    /**
     * @Route("/account", name="account_index")
     * @IsGranted("ROLE_USER")
     */
    public function accountIndex(){
        return $this->render('account/index.html.twig',[
            'user' => $this->getUser()
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'inscription
     * @Route("/register", name="account_register")
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder){
        $user = new User();
        $form = $this->createForm(RegistrationType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $hash= $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre compte a bien été créé'
            );
            return $this->redirectToRoute('account_login');
        }
        return $this->render('account/registration.html.twig',[
            "myForm" => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher et de traiter le formulaire de modification de profil
     * @Route("/account/email-update", name="account_email")
     * @IsGranted("ROLE_USER")
     */
    public function updateEmail(Request $request, EntityManagerInterface $manager){
        $user = $this->getUser(); 
        $form = $this->createForm(ModificationEmailType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'email a été enregistré avec succès"
            );
            return $this->redirectToRoute('account_index');
        }
        return $this->render('account/email.html.twig',[
            'myForm' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier le mot de passe
     * @Route("/account/password-update", name="account_password")
     * @IsGranted("ROLE_USER")
     */
    public function updatePassword(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder){
        $passwordUpdate = new PasswordUpdate();
        $user = $this->getUser();
        $form = $this->createForm(ModificationPasswordType::class,$passwordUpdate);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if(!password_verify($passwordUpdate->getOldPassword(),$user->getPassword())){
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez inséré n'est pas le bon mot de passe"));
            }else{
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user,$newPassword);

                $user->setPassword($hash);
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Votre mot de passe a bien été modifié'
                );

                return $this->redirectToRoute('account_index');
            }
        }

        return $this->render('account/password.html.twig',[
            'myForm' => $form->createView()
        ]);
    }
}
