<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Form\SearchVehiculeType;
use App\Repository\VehiculeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class VehiculeController extends AbstractController
{
    #[Route('/vehicule', name: 'app_vehicule')]
    public function index(VehiculeRepository $vehiculeRepository ,SessionInterface $session): Response
    {
        $form=$this->createForm(SearchMemberType::class);
        $result = $session->get('search_result');
        $session->remove('search_result');
        $vehicules = $result ?? $vehiculeRepository->findAll();
        return $this->render('/vehicule/index.html.twig', [
            'vehicules' => $vehicules,'formSearch'=>$form
        ]);
    }


    #[Route('/vehicule/new', name: 'app_vehicule_add')]
    #[Route('/vehicule/edit/{id}', name: 'app_vehicule_edit')]
    public function newEdit(Vehicule $vehicule=null,Request $request ,EntityManagerInterface $manager): Response
    {
        $etat=1;
        if(!$vehicule){
            $member=new Vehicule();
            $etat=0;
        }
        $form=$this->createFormBuilder($vehicule)
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
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Admin' => '1',
                    'Utilisateur' => '0',
                ],
                'expanded' => false, 
                'multiple' => false,
                'label' => 'Statut',
            ])
            ->add('date_enregistrement',DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date',
                'attr' => ['class' => 'datepicker'],
            ])
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($member);
            $manager->flush();
        }
        return $this->render('member/addMember.html.twig',['formAddEditMember'=>$form->createView() ,'etatButton'=>$etat]);
    }

    #[Route('/vehicule/search', name: 'app_vehicule_search', methods:['GET','POST'])]
    public function search(Request $request,EntityManagerInterface $manager,SessionInterface $session): Response
    {
        $form=$this->createForm(SearchVehiculeType::class);
        $form->handleRequest($request);
        $qb = $manager->createQueryBuilder();
        if($form->isSubmitted() && $form->isValid()){
            $qb->select('v')
                    ->from(Vehicule::class, 'v')
                    ->where($qb->expr()->like('LOWER(v.titre)', ':searchTerm1'))
                    ->orWhere($qb->expr()->like('LOWER(v.marque)', ':searchTerm1'))
                    ->orWhere($qb->expr()->like('LOWER(v.modele)', ':searchTerm1'))
                    ->orWhere($qb->expr()->like('LOWER(v.prix)', ':searchTerm1'))
                    ->setParameter('searchTerm1', '%' . strtolower($form->get('champs')->getData()) . '%');
            $result = $qb->getQuery()->getResult();
            $session->set('search_result', $result);
        }
        return $this->redirectToRoute('app_vehicule');
    }
    
    #[Route('/vehicule/delete/{id?0}', name: 'app_vehicule_delete')]
    public function delete(Vehicule $vehicule,VehiculeRepository $vehiculeRepository): Response
    {
        $vehiculeRepository->remove($vehicule);
        return $this->redirectToRoute('app_vehicule');
    }
}
