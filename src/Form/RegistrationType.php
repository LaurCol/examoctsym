<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',EmailType::class, $this->getConfiguration("Email", "Votre adresse e-mail..."))
            ->add('password', PasswordType::class, $this->getConfiguration('Mot de passe',"Entrez votre mot de passe..."))
            ->add("passwordConfirm", PasswordType::class, $this->getConfiguration('Confirmation du mot de passe',"Confirmez votre mot de passe..."))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
