<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{
    /**
     * @Route("/", name="home_page")
     */
    public function index(CategoryRepository $categoryRepository, BookRepository $bookRepository): Response
    {
        $user = $this->getUser();
        $categories = $categoryRepository->findAll();
        return $this->render('home_page/index.html.twig', [
            'categories' => $categories,
        ]);
    }
}
