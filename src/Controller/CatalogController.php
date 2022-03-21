<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Genre;
use App\Entity\Book;
use App\Entity\Author;
use App\Entity\User;

class CatalogController extends AbstractController
{
    #[Route('/catalog', name: 'catalog_index')]
    public function index(ManagerRegistry $mr): Response
    {
        $count = [];

        $count['books'] = $mr->getRepository(Book::class)->count([]);
        $count['users'] = $mr->getRepository(User::class)->count([]);
        $count['genres'] = $mr->getRepository(Genre::class)->count([]);
        $count['authors'] = $mr->getRepository(Author::class)->count([]);

        return $this->render('catalog/index.html.twig', ['count' => $count]);
    }
}
