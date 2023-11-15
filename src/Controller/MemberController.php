<?php

namespace App\Controller;
use DateTime;
use App\Entity\Member;
use App\Form\AddEditMemberType;
use App\Form\MemberLoginType;
use App\Form\SearchMemberType;
use App\Repository\CommandeRepository;
use App\Repository\MemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

class MemberController extends AbstractController
{
    #[Route('/member', name: 'app_member')]
    public function index(MemberRepository $memberRepository,SessionInterface $session): Response
    {
        $form=$this->createForm(SearchMemberType::class);
        $result = $session->get('search_result');
        $session->remove('search_result');
        $members = $result ?? $memberRepository->findAll();
        return $this->render('/member/index.html.twig', [
            'members' => $members,'formSearch'=>$form
        ]);
    }

    #[Route('/member/new', name: 'app_member_add')]
    #[Route('/member/edit/{id}', name: 'app_member_edit')]
    public function newEdit(Member $member=null,Request $request ,EntityManagerInterface $manager): Response
    {
        $etat=1;
        if(!$member){
            $member=new Member();
            $etat=0;
        }
        $form=$this->createForm(AddEditMemberType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $member->setDateEnregistrement(new DateTime());
            $manager->persist($member);
            $manager->flush();
        }
        return $this->render('member/addMember.html.twig',['formAddEditMember'=>$form->createView() ,'etatButton'=>$etat]);
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
    public function login(Member $member=null , MemberRepository $memberRepository ,Request $request,SessionInterface $session): Response
    {
        $member=new Member();
        $message=null;
        $id=null;
        $form=$this->createForm(MemberLoginType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $memberfindRepo=$memberRepository->findOneBy(['email'=>$form->get('pseudo')->getData() ,'pseudo'=>$form->get('pseudo')->getData()]);
            $memberfind=$memberfindRepo[0];
            if($memberfind!=null && password_verify($member->getMdp(),$memberfind->getMdp())){
                $id=$memberfind->getId();
                $session->set('idMember', $id);
                return $this->redirectToRoute('app_home', ['idMember'=>$id]);
            }else{
                $message="Attention !! Login ou mot de passe est incorrecte";
                return $this->render('member/loginMember.html.twig', ['formLogin'=>$form->createView(), 'message'=>$message]);
            }
        }
        return $this->render('member/loginMember.html.twig', ['formLogin'=>$form->createView(),'message'=>$message]);
    }


    #[Route('/member/logout', name: 'app_member_logout')]
    public function logout(SessionInterface $session): Response
    {
        $session->remove('idMember');
        return $this->redirectToRoute('app_home');
    }
}
