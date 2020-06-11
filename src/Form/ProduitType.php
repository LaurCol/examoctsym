<?php

namespace App\Form;

use App\Entity\Produit;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProduitType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::class,$this->getConfiguration("Nom","Entrez le nom du produit..."))
            ->add('categorie',ChoiceType::class,$this->getConfiguration("Catégorie","",[
                'choices' => [
                    'Instruments' => "Instruments",
                    "Accessoires"=>"Accessoires",
                    "CD"=>"CD",
                    "Vinyles"=>"Vinyles"
                    ]
                ]))
            ->add('prix',MoneyType::class, $this->getConfiguration('Prix','Indiquez le prix du produit...'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
