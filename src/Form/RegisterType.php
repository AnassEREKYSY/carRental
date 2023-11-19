<?php

namespace App\Form;

use App\Entity\Member;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('pseudo' ,TypeTextType::class,['attr'=>['class'=> 'form-control registerInput',]])
        ->add('nom' ,TypeTextType::class, ['attr'=>['class'=> 'form-control registerInput',]])
        ->add('prenom' ,TextType::class, ['attr'=>['class'=> 'form-control registerInput',]])
        ->add('email' ,TextType::class, ['attr'=>['class'=> 'form-control registerInput',]])
        ->add('mdp',PasswordType::class,['label'=>'Mot de passe','attr'=>['class'=> 'form-control registerInput',]])
        ->add('civilite', ChoiceType::class, [
            'choices' => [
                'Homme' => '1',
                'Femme' => '0',
            ],
            'expanded' => false, 
            'multiple' => false, 
            'label' => 'Civilité',
            'attr'=>['class'=> 'form-control registerInput',
        ] ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Member::class,
        ]);
    }
}
