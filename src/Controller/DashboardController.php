<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(BookRepository $bookRepository): Response
    {
        $user = $this->getUser();
        $newestBooks = $bookRepository->findNewestBooks();
        $recomendedForYou = $bookRepository->findRecommendedForYou($user);
        $myBooks = $bookRepository->findMyBooks($user);
        return $this->render('dashboard/index.html.twig', [
            'newestBooks' => $newestBooks,
            'recomendedForYou' => $recomendedForYou,
            'myBooks' => $myBooks,
        ]);
    }
}
