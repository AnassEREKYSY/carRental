<?php

namespace App\Controller;

use App\Form\HomeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(Request $request,SessionInterface $session): Response
    {
        $form=$this->createForm(HomeType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $date_debut=$form->get('datedebut')->getData();
            $date_fin=$form->get('datefin')->getData();
            $session->set('dateDepart',$date_debut);
            $session->set('dateFin',$date_fin);
            return $this->forward('App\\Controller\\VehiculeController::index',['dateDebut'=>$date_debut,'dateFin'=>$date_fin]);
        }
        return $this->render('home.html.twig', ['formDatePicker'=>$form->createView() ]);
    }
}
