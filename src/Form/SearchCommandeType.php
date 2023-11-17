<?php

namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;

class SearchCommandeType extends AbstractType
{
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setAction($this->urlGenerator->generate('app_commande_search'))
            ->add('champs',TypeTextType::class,['mapped'=>false,'label'=>false , 'attr'=>['class'=> 'form-control searchInput',]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
