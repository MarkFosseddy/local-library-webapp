<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Book;
use App\Entity\Author;
use App\Entity\Genre;
use App\Form\BookFormType;
use App\Form\DeleteByIdFormType;

#[Route('/catalog')]
class BookController extends AbstractController
{
    #[Route('/book', name: 'book_index')]
    public function index(ManagerRegistry $mr): Response
    {
        $books = $mr->getRepository(Book::class)->findAll();
        return $this->render('book/index.html.twig', ['books' => $books]);
    }

    #[Route('/book/create', name: 'book_create')]
    public function create(Request $req, ManagerRegistry $mr): Response
    {
        $form = $this->createForm(BookFormType::class, new Book());

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $book = $form->getData();
            $mr->getRepository(Book::class)->add($book);

            return $this->redirectToRoute('book_index');
        }

        return $this->renderForm('book/create.html.twig', ['form' => $form]);
    }

    #[Route('/book/{id}', name: 'book_show')]
    public function show(ManagerRegistry $mr, $id): Response
    {
        $book = $mr->getRepository(Book::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException('The book does not exist');
        }

        return $this->render('book/show.html.twig', ['book' => $book]);
    }


    #[Route('/book/{id}/delete', name: 'book_delete')]
    public function delete(Request $req, ManagerRegistry $mr, $id): Response
    {
        $book = $mr->getRepository(Book::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException('The book does not exist');
        }

        $form = $this->createForm(DeleteByIdFormType::class, null, [
            'id' => $id
        ]);

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $deleteId = $form->get('id')->getData();

            if ($deleteId != $id) {
                throw $this->createNotFoundException('The book does not exist');
            }

            $mr->getRepository(Book::class)->remove($book);

            return $this->redirectToRoute('book_index');
        }

        return $this->renderForm('book/delete.html.twig', [
            'form' => $form,
            'book' => $book
        ]);
    }

    #[Route('/book/{id}/update', name: 'book_update')]
    public function update(Request $req, ManagerRegistry $mr, $id): Response
    {
        $book = $mr->getRepository(Book::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException('The book does not exist');
        }

        $form = $this->createForm(BookFormType::class, $book);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $book = $form->getData();
            $mr->getManager()->flush();

            return $this->redirectToRoute('book_show', ['id' => $id]);
        }

        return $this->renderForm('book/update.html.twig', ['form' => $form]);
    }
}
