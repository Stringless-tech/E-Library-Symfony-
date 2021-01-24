<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\SearchType;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{

    private $books;

    public function __construct(BookRepository $bookRepository)
    {
        $this->books = $bookRepository;
    }
    /**
     * @Route("/", name="home_page")
     */
    public function index(Request $request,CategoryRepository $categoryRepository): Response
    {
        $test = $this->books->findTop5RatedBooks();

        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);
        if($form->isSubmitted())
        {
            $slug = $request->request->get('app_search')['Wyszukaj'];
            return $this->redirect($this->generateUrl('search-results',['slug' => $slug]));
        }
        $search = $form->createView();

        $categories = $categoryRepository->findAll();
        return $this->render('home_page/index.html.twig', [
            'categories' => $categories,
            'test' => $test,
            'form' => $search
        ]);
    }

    /**
     * @Route("/search-results", name="search-results")
     */
    public function searchResults(Request $request)
    {
        $slug = $request->query->get('slug');
        $books = $this->books->searchResults($slug);
        return $this->render('home_page/search.html.twig',[
            'books' => $books
        ]);
    }
}
