<?php

namespace App\Controller;

use App\Entity\Member;
use App\Form\AddEditMemberType;
use App\Form\ProfilType;
use App\Form\RegisterType;
use App\Form\SearchMemberType;
use App\Repository\CommandeRepository;
use App\Repository\MemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MemberController extends AbstractController
{
    #[Route('/member', name: 'app_member')]
    public function index(MemberRepository $memberRepository,SessionInterface $session): Response
    {
        $user=$this->getUser();
        $statutUser=null;
        $form=$this->createForm(SearchMemberType::class);
        $result = $session->get('search_result');
        $session->remove('search_result');
        $members = $result ?? $memberRepository->findAll();
        if($user && $user instanceof Member){
            $statutUser=$memberRepository->findOneBy(['id'=>$user->getId()])->getStatut();
            $userId=$user->getId();
        }
        return $this->render('/member/index.html.twig', [
            'members' => $members,'formSearch'=>$form,'statutUser'=>$statutUser,'idMember'=>$userId
        ]);
    }

    #[Route('/member/new', name: 'app_member_add')]
    #[Route('/member/edit/{id}', name: 'app_member_edit')]
    public function newEdit(Member $member=null,Request $request ,EntityManagerInterface $manager
                            ,FormInterface $formProfil = null): Response
    {
        $operationType=0;
        $etat=1;
        $form=null;
        if(!$member){
            $member=new Member();
            $etat=0;
        }
        if($formProfil!=null){
            $form=$this->createForm(ProfilType::class,$member);
            $operationType=1;
        }else{
            $form=$this->createForm(AddEditMemberType::class,$member);
        }
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $avecHachageMdp=password_hash($member->getMdp(), PASSWORD_BCRYPT);
            $member->setMdp($avecHachageMdp);
            $member->setStatut(0);
            $member->setDateEnregistrement(new \DateTime());
            $manager->persist($member);
            $manager->flush();
            flash()->addSuccess("L'operation est passé avec succés");
        }
        if($operationType==0){
            return $this->render('member/addMember.html.twig',['formAddEditMember'=>$form->createView() ,'etatButton'=>$etat,]);
        }else{
            return $this->render('member/profil.html.twig',['formProfil'=>$form->createView(),]);
        }
    }

    #[Route('/member/search', name: 'app_member_search', methods:['GET','POST'])]
    public function search(Request $request,EntityManagerInterface $manager,SessionInterface $session): Response
    {
        $form=$this->createForm(SearchMemberType::class);
        $form->handleRequest($request);
        $qb = $manager->createQueryBuilder();
        if($form->isSubmitted() && $form->isValid()){
            $qb->select('m')
                    ->from(Member::class, 'm')
                    ->where($qb->expr()->like('LOWER(m.nom)', ':searchTerm1'))
                    ->orWhere($qb->expr()->like('LOWER(m.prenom)', ':searchTerm1'))
                    ->orWhere($qb->expr()->like('LOWER(m.pseudo)', ':searchTerm1'))
                    ->orWhere($qb->expr()->like('LOWER(m.email)', ':searchTerm1'))
                    ->setParameter('searchTerm1', '%' . strtolower($form->get('champs')->getData()) . '%');
            $result = $qb->getQuery()->getResult();
            $session->set('search_result', $result);
        }
        return $this->redirectToRoute('app_member');
    }
    
    #[Route('/member/delete/{id?0}', name: 'app_member_delete')]
    public function delete(Member $member , MemberRepository $memberRepository ,
                            CommandeRepository $commandeRepository): Response
    {
        $commandeRepository->removeByMemberId($member->getId());
        $memberRepository->remove($member);
        return $this->redirectToRoute('app_member');
    }


    #[Route('/member/login', name: 'app_member_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('member/loginMember.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error
        ]);
    }


    #[Route('/member/logout', name: 'app_member_logout')]
    public function logout(): Response
    {
        throw new \Exception('');
        flash()->addInfo("Logout fait avec succés");
    }

    #[Route('/member/profil/{id}', name: 'app_profil')]
    public function profil(Member $member=null,Request $request): Response
    {
        $message=null;
        $form=$this->createForm(ProfilType::class,$member);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            return $this->forward('App\\Controller\\MemberController::newEdit',[
                'member'=>$member,'formProfil'=>$form
            ]);
        }
        return $this->render('member/profil.html.twig',['formProfil'=>$form->createView(), 'message'=>$message,]);
    }

    #[Route('/member/register', name: 'app_register')]
    public function register(Member $member=null,Request $request,EntityManagerInterface $manager
    ): Response
    {
        $member=new Member();
        $form=$this->createForm(RegisterType::class,$member);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $avecHachageMdp=password_hash($form->get('mdp')->getData(), PASSWORD_BCRYPT);
            $member->setMdp($avecHachageMdp);
            $member->setStatut(0);
            $member->setDateEnregistrement(new \DateTime());
            $manager->persist($member);
            $manager->flush();
            flash()->addSuccess("L'operation est passé avec succés");
        }
        return $this->render('member/register.html.twig',['formRegister'=>$form->createView()]);
    }
}
