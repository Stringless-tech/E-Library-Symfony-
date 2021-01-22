<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{
    /**
     * @Route("/", name="home_page")
     */
    public function index(Request $request,CategoryRepository $categoryRepository, BookRepository $bookRepository): Response
    {
        $user = $this->getUser();
        $test = $bookRepository->findTop5RatedBooks();
        $search = $this->search($request,$bookRepository);
        $categories = $categoryRepository->findAll();
        return $this->render('home_page/index.html.twig', [
            'categories' => $categories,
            'test' => $test,
            'form' => $search
        ]);
    }

    public function search($request,$bookRepository)
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);
        if($form->isSubmitted())
        {
            //$slug = $request->request->get('app_search')['Wyszukaj'];
            //$books = $bookRepository->searchResults($slug);
            return $this->redirect($this->generateUrl('search-results'));
        }
        return $form->createView();
    }
    /**
     * @Route("/search-results", name="search-results")
     */
    public function searchResults(BookRepository $bookRepository)
    {
        //
        return 'asdasdasd';
    }
}
