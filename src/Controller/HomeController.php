<?php

namespace App\Controller;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        $form=$this->createFormBuilder()
            ->add('datedebut', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'datepicker']])
            ->add('datefin', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'datepicker']])
            ->getForm();
        if($form->isSubmitted() && $form->isValid()){
           
            //return $this->redirectToRoute('app_article_details',['id'=>$member->getId()]);
        }
        return $this->render('home.html.twig', ['formDatePicker'=>$form->createView() ]);
    }
}
