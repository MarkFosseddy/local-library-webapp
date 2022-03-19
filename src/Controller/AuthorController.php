<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Author;
use App\Form\AuthorFormType;

#[Route('/catalog')]
class AuthorController extends AbstractController
{
    #[Route('/author', name: 'author_index')]
    public function index(ManagerRegistry $mr): Response
    {
        $authors = $mr->getRepository(Author::class)->findAll();
        return $this->render('author/index.html.twig', ['authors' => $authors]);
    }

    #[Route('/author/create', name: 'author_create')]
    public function create(Request $req, ManagerRegistry $mr): Response
    {
        $form = $this->createForm(AuthorFormType::class, new Author());
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $author = $form->getData();

            $m = $mr->getManager();
            $m->persist($author);
            $m->flush();

            return $this->redirectToRoute('author_index');
        }

        return $this->renderForm('author/create.html.twig', ['form' => $form]);
    }

    #[Route('/author/{id}', name: 'author_show')]
    public function show(ManagerRegistry $mr, $id): Response
    {
        $author = $mr->getRepository(Author::class)->find($id);

        if (!$author) {
            throw $this->createNotFoundException('The author does not exist');
        }

        return $this->render('author/show.html.twig', ['author' => $author]);
    }


    #[Route('/author/{id}/delete', name: 'author_delete')]
    public function delete(Request $req, ManagerRegistry $mr, $id): Response
    {
        $author = $mr->getRepository(Author::class)->find($id);

        if (!$author) {
            throw $this->createNotFoundException('The author does not exist');
        }

        $form = $this->createFormBuilder()
            ->add('id', HiddenType::class, ['data' => $id])
            ->getForm();

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $deleteId = $form->get('id')->getData();

            if ($deleteId != $id) {
                throw $this->createNotFoundException('The author does not exist');
            }

            $m = $mr->getManager();
            $m->remove($author);
            $m->flush();

            return $this->redirectToRoute('author_index');
        }

        return $this->renderForm('author/delete.html.twig', [
            'form' => $form,
            'author' => $author
        ]);
    }

    #[Route('/author/{id}/update', name: 'author_update')]
    public function update(Request $req, ManagerRegistry $mr, $id): Response
    {
        $author = $mr->getRepository(Author::class)->find($id);

        if (!$author) {
            throw $this->createNotFoundException('The author does not exist');
        }

        $form = $this->createForm(AuthorFormType::class, $author);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $author = $form->getData();
            $mr->getManager()->flush();

            return $this->redirectToRoute('author_show', ['id' => $id]);
        }

        return $this->renderForm('author/update.html.twig', ['form' => $form]);
    }
}
