<?php

namespace App\Form;

use App\Entity\Member;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('pseudo')
        ->add('nom')
        ->add('prenom')
        ->add('email')
        ->add('mdp',PasswordType::class,['label'=>'Mot de passe'])
        ->add('civilite', ChoiceType::class, [
            'choices' => [
                'Homme' => '1',
                'Femme' => '0',
            ],
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
