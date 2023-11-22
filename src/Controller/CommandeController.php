<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Member;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\AddEditCommandeType;
use App\Form\SearchCommandeType;
use App\Repository\CommandeRepository;
use App\Repository\MemberRepository;
use App\Repository\VehiculeRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'app_commande')]
    public function index(CommandeRepository $commandeRepository,SessionInterface $session,
                            VehiculeRepository $vehiculeRepository,MemberRepository $memberRepository,$op=0): Response
    {
        $form=$this->createForm(SearchCommandeType::class);
        $result = $session->get('search_result');
        $session->remove('search_result');
        if($result){
            $vehicule=$vehiculeRepository->findOneBy(['id'=>$result[0]->getIdVehicule()->getId()]);
            $member=$memberRepository->findOneBy(['id'=>$result[0]->getIdMember()->getId()]);
            $result[0]->setIdVehicule($vehicule);
            $result[0]->setIdMember($member);
        }
        $commandes = $result ?? $commandeRepository->findAll();
        if((int)$op && (int)$op==1){
            flash()->addSuccess("L'operation est passé avec succés");
        }
        return $this->render('/commande/index.html.twig', [
            'commandes' => $commandes,'formSearch'=>$form
        ]);
    }

    
    #[Route('/commande/new/{id?0}', name: 'app_commande_add')]
    #[Route('/commande/edit/{id}', name: 'app_commande_edit')]
    public function newEdit(Commande $commande=null,Request $request ,MemberRepository $memberRepository,
                                EntityManagerInterface $manager, VehiculeRepository $vehiculeRepository,
                                $id,SessionInterface $session): Response
    {
        $etat=1;
        $memberId=null;
        $vehiculeId=null;
        if(!$commande){
            $commande=new Commande();
            $etat=0;
        }
        $form=$this->createForm(AddEditCommandeType::class,$commande);
        $form->handleRequest($request);
        $dateDepart=$session->get("dateDepart");
        $dateFin=$session->get("dateFin");
        if($id){
            $user=$this->getUser();
            if( $user instanceof Member)
                $memberId=$user->getId();
            $vehiculeId=(int)$id;
        }else{
            $memberId=$form->get('member')->getData();
            $vehiculeId=$form->get('vehicule')->getData();
        }
        if(($form->isSubmitted() && $form->isValid()) || ($id!=0 && $memberId!=0)){
            $commande->setDateEnregistrement(new \DateTime());
            $commande->setDateDepart($dateDepart);
            $commande->setDateFin($dateFin);
            $member=$memberRepository->findOneBy(['id'=>$memberId]);
            $vehicule=$vehiculeRepository->findOneBy(['id'=>$vehiculeId]);
            $vehicule->setAvailable("non");
            $commande->setIdVehicule($vehicule);
            $commande->setIdMember($member);
            $days=$commande->getDateDepart()->diff($commande->getDateFin())->days;
            $prix=$days*$commande->getIdVehicule()->getPrixJournalier();
            $commande->setPrixTotal($prix);
            $manager->persist($commande);
            $manager->flush();       
            $commande=1;
            if($id!=0 && $memberId!=0){
                flash()->addSuccess("L'operation est passé avec succés");
                return $this->forward('App\\Controller\\VehiculeController::index');
            }else{
                flash()->addSuccess("L'operation est passé avec succés");
                return $this->redirectToRoute('app_commande');
            }
        }
        return $this->render('commande/addCommande.html.twig',['formAddEditCommande'=>$form->createView() ,'etatButton'=>$etat]);
    }

    #[Route('/commande/search', name: 'app_commande_search', methods:['GET','POST'])]
    public function search(Request $request,EntityManagerInterface $manager,SessionInterface $session): Response
    {
        $form=$this->createForm(SearchCommandeType::class);
        $form->handleRequest($request);
        $qb = $manager->createQueryBuilder();
        if($form->isSubmitted() && $form->isValid()){
            $qb->select('c')
                ->from(Commande::class, 'c')
                ->leftJoin('c.id_member', 'm') 
                ->leftJoin('c.id_vehicule', 'v')
                ->where(
                    $qb->expr()->orX(
                        $qb->expr()->like('LOWER(m.pseudo)', ':searchTerm1'),
                        $qb->expr()->like('LOWER(m.email)', ':searchTerm1'),
                        $qb->expr()->like('LOWER(m.nom)', ':searchTerm1'),
                        $qb->expr()->like('LOWER(m.prenom)', ':searchTerm1'),
                        $qb->expr()->like('LOWER(v.marque)', ':searchTerm1'),
                        $qb->expr()->like('LOWER(v.titre)', ':searchTerm1'),
                        $qb->expr()->like('LOWER(v.modele)', ':searchTerm1'),
                        $qb->expr()->like('LOWER(c.date_depart)', ':searchTerm1'),
                        $qb->expr()->like('LOWER(c.date_fin)', ':searchTerm1')
                    )
                )
                ->setParameter('searchTerm1', '%' . strtolower($form->get('champs')->getData()) . '%');
            
            $result = $qb->getQuery()->getResult();
            $session->set('search_result', $result);
        }
        return $this->redirectToRoute('app_commande');
    }
    
    #[Route('/commande/delete/{id?0}', name: 'app_commande_delete')]
    public function delete(Commande $commande,CommandeRepository $commandeRepository): Response
    {
        $commande->getIdVehicule()->setAvailable("oui");
        $commandeRepository->remove($commande);
        return $this->redirectToRoute('app_commande',['op'=>1]);
    }

    #[Route('/commande/history', name: 'app_commande_history')]
    public function history(CommandeRepository $commandeRepository): Response
    {
        $user=$this->getUser();
        $userCommande=null;
        $id=null;
        if($user instanceof Member){
            $userCommande=$user;
            $id=$userCommande->getId();
        }
        $commandes=$commandeRepository->findBy(['id_member'=>$userCommande]);
        return $this->render('/commande/history.html.twig', ['commandes' => $commandes,'idMember' => $id]);
    }
}
