<?php

namespace App\Form;

use App\Entity\Member;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('pseudo',TypeTextType::class,['attr'=>['class'=> 'form-control profilInput',]])
        ->add('nom',TypeTextType::class,['attr'=>['class'=> 'form-control profilInput',]])
        ->add('prenom',TypeTextType::class,['attr'=>['class'=> 'form-control profilInput',]])
        ->add('email',TypeTextType::class,['attr'=>['class'=> 'form-control profilInput',]])
        ->add('mdp',PasswordType::class,['label'=>'Mot de passe','attr'=>['class'=> 'form-control profilInput',]])
        ->add('civilite', ChoiceType::class, [
            'choices' => [
                'Homme' => '1',
                'Femme' => '0',
            ],
            'attr'=>['class'=> 'form-control profilInput',],
            'expanded' => false, 
            'multiple' => false, 
            'label' => 'Civilité',
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Member::class,
        ]);
    }
}
