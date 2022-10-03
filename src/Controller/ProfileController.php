<?php

namespace App\Controller;

use App\Form\ProfileEditFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    #[Route('/profile', name: 'main_profile_index')]
    public function index(): Response
    {
        return $this->render('main/profile/index.html.twig');
    }

    #[Route('/profile/edit', name: 'main_profile_edit')]
    public function edit(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileEditFormType::class, $user);
        /*dd($form->handleRequest($request));*/
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('main_profile_index');
        }

        return $this->render('main/profile/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
