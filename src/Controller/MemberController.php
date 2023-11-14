<?php

namespace App\Controller;

use App\Entity\Member;
use App\Repository\CommandeRepository;
use App\Repository\MemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MemberController extends AbstractController
{
    #[Route('/member', name: 'app_member')]
    public function index(MemberRepository $memberRepository): Response
    {
        $members=$memberRepository->findAll();
        return $this->render('/member/index.html.twig', [
            'members' => $members,
        ]);
    }

    #[Route('/member/new', name: 'app_member_add')]
    #[Route('/member/edit/{id}', name: 'app_member_edit')]
    public function newEdit(Member $member=null,Request $request ,EntityManagerInterface $manager,): Response
    {
        $etat=1;
        if(!$member){
            $member=new Member();
            $etat=0;
        }
        $form=$this->createFormBuilder($member)
            ->add('pseudo')
            ->add('nom')
            ->add('prenom')
            ->add('email')
            ->add('mdp',PasswordType::class)
            ->add('civilite')
            ->add('statut')
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

    #[Route('/member/search/{id}', name: 'app_member_search')]
    public function search(): Response
    {
        return $this->render('member/index.html.twig', [
            'controller_name' => 'MemberController',
        ]);
    }
    
    #[Route('/member/delete/{id?0}', name: 'app_member_delete')]
    public function delete(Member $member , MemberRepository $memberRepository ,
                            CommandeRepository $commandeRepository): Response
    {


        $commandeRepository->removeByMemberId($member->getId());
        $memberRepository->remove($member);
        // $articleRepository->remove($article,true);
        // return $this->redirectToRoute('app_article' , ['articles'=>$articleRepository->findAll()]);


        return $this->render('member/index.html.twig', [
            'controller_name' => 'MemberController',
        ]);
    }
}
