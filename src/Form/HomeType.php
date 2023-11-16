<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HomeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('datedebut',DateType::class, [
            'widget' => 'single_text',
            'label' => 'Date debut',
            'attr' => ['class' => 'datepickerItem'],
        ])
        ->add('datefin', DateType::class, [
            'widget' => 'single_text',
            'label' => 'Date fin',
            'attr' => ['class' => 'datepickerItem'],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
