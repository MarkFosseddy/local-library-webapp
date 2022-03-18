<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    #[Route('/genre/create', name: 'genre_create')]
    public function create(Request $req, ManagerRegistry $mr): Response
    {
        $form = $this->createForm(GenreType::class, new Genre());
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

    #[Route('/genre/{id}', name: 'genre_show')]
    public function show(ManagerRegistry $mr, $id): Response
    {
        $genre = $mr->getRepository(Genre::class)->find($id);

        if (!$genre) {
            throw $this->createNotFoundException('The genre does not exist');
        }

        return $this->render('genre/show.html.twig', ['genre' => $genre]);
    }

    #[Route('/genre/{id}/delete', name: 'genre_delete')]
    public function delete(ManagerRegistry $mr, $id): Response
    {
        $genre = $mr->getRepository(Genre::class)->find($id);

        if (!$genre) {
            throw $this->createNotFoundException('The genre does not exist');
        }

        $m = $mr->getManager();
        $m->remove($genre);
        $m->flush();

        return $this->redirectToRoute('genre_index');
    }

    #[Route('/genre/{id}/update', name: 'genre_update')]
    public function update(Request $req, ManagerRegistry $mr, $id): Response
    {
        $genre = $mr->getRepository(Genre::class)->find($id);

        if (!$genre) {
            throw $this->createNotFoundException('The genre does not exist');
        }

        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $genre = $form->getData();
            $mr->getManager()->flush();

            return $this->redirectToRoute('genre_show', [
                'id' => $genre->getId()
            ]);
        }

        return $this->renderForm('genre/update.html.twig', ['form' => $form]);
    }
}
