<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Genre;
use App\Form\GenreType;

#[Route('/catalog')]
class GenreController extends AbstractController
{
    #[Route('/genre', name: 'genre_index')]
    public function index(ManagerRegistry $mr): Response
    {
        $genres = $mr->getRepository(Genre::class)->findAll();
        return $this->render('genre/index.html.twig', ['genres' => $genres]);
    }

    #[Route('/genre/create', methods: ['GET', 'POST'], name: 'genre_create')]
    public function create(Request $req, ManagerRegistry $mr): Response
    {
        $genre = new Genre();
        $form = $this->createForm(GenreType::class, $genre);

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $genre = $form->getData();

            $manager = $mr->getManager();
            $manager->persist($genre);
            $manager->flush();

            return $this->redirectToRoute('genre_index');
        }

        return $this->renderForm('genre/create.html.twig', ['form' => $form]);
    }
}
