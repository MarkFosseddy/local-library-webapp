<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class AuthController extends AbstractController
{
    #[Route('/', name: 'login_user')]
    public function login(): Response
    {
        return $this->render('auth/login.html.twig');
    }

    #[Route('/register', name: 'register_user')]
    public function registerUser(
        Request $req,
        UserPasswordHasherInterface $hasher,
        ManagerRegistry $mr
    ): Response
    {
        $form = $this->createForm(RegistrationFormType::class, new User());
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $user->hashPassword($hasher);
            $user->setRoles([User::ROLE_USER]);

            $mr->getRepository(User::class)->add($user);

            return $this->redirectToRoute('catalog_index');
        }

        return $this->renderForm('auth/register.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/admin/register', name: 'register_admin')]
    public function registerAdmin(
        Request $req,
        UserPasswordHasherInterface $hasher,
        ManagerRegistry $mr
    ): Response
    {
        $form = $this->createForm(RegistrationFormType::class, new User());
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $user->hashPassword($hasher);
            $user->setRoles([User::ROLE_ADMIN]);

            $mr->getRepository(User::class)->add($user);

            return $this->redirectToRoute('catalog_index');
        }

        return $this->renderForm('auth/register.html.twig', [
            'form' => $form
        ]);
    }
}
