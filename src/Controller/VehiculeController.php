<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Form\AddEditVehiculeType;
use App\Form\SearchVehiculeType;
use App\Repository\VehiculeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class VehiculeController extends AbstractController
{
    #[Route('/vehicule', name: 'app_vehicule')]
    public function index(VehiculeRepository $vehiculeRepository ,SessionInterface $session): Response
    {
        $form=$this->createForm(SearchVehiculeType::class);
        $result = $session->get('search_result');
        $session->remove('search_result');
        $vehicules = $result ?? $vehiculeRepository->findAll();
        return $this->render('/vehicule/index.html.twig', [
            'vehicules' => $vehicules,'formSearch'=>$form
        ]);
    }


    #[Route('/vehicule/new', name: 'app_vehicule_add')]
    #[Route('/vehicule/edit/{id}', name: 'app_vehicule_edit')]
    public function newEdit(Vehicule $vehicule=null,Request $request ,
                                EntityManagerInterface $manager,SluggerInterface $slugger): Response
    {
        $etat=1;
        if(!$vehicule){
            $vehicule=new Vehicule();
            $etat=0;
        }
        $form=$this->createForm(AddEditVehiculeType::class,$vehicule);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $vehicule->setDateEnregistrement(new \DateTime());
            if($form->get('photo')->getData() !=null){
                $File = $form->get('photo')->getData();
                if ($File) {
                    $originalFilename = pathinfo($File->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$File->guessExtension();
                    try {
                        $File->move(
                            $this->getParameter('vehicules_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $vehicule->setPhoto($newFilename);
                }
            }
            $manager->persist($vehicule);
            $manager->flush();
        }
        return $this->render('vehicule/addVehicule.html.twig',['formAddEditVehicule'=>$form->createView() ,'etatButton'=>$etat]);
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
