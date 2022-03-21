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
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    #[Route('/', name: 'login')]
    public function login(AuthenticationUtils $auth): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('catalog_index');
        }

        return $this->render('auth/login.html.twig', [
            'username' => $auth->getLastUsername(),
            'error' => $auth->getLastAuthenticationError()
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(AuthenticationUtils $auth): Response
    {
        throw new \Exception('Probably forgot to add logout to security.yaml');
    }

    #[Route('/register', name: 'register')]
    public function register(
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

            return $this->redirectToRoute('login');
        }

        return $this->renderForm('auth/register.html.twig', [
            'form' => $form
        ]);
    }
}
