<?php

namespace App\Form;

use App\Entity\Vehicule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;

class AddEditVehiculeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre',TypeTextType::class,['attr'=>['class'=> 'form-control addInput',]])
            ->add('marque',TypeTextType::class,['attr'=>['class'=> 'form-control addInput',]])
            ->add('modele',TypeTextType::class,['attr'=>['class'=> 'form-control addInput',]])
            ->add('description', TextareaType::class,['attr'=>['class'=> 'form-control addInput',]])
            ->add('photo', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypesMessage' => 'Please upload a valid Image',
                    ])
                ],
            ])
            ->add('prix_journalier',TypeTextType::class,['attr'=>['class'=> 'form-control addInput',]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicule::class,
        ]);
    }
}
