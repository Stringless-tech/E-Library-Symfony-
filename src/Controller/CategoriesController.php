<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoriesController extends AbstractController
{
    private $category;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->category = $categoryRepository;
    }
    /**
     * @Route("/categories", name="categories")
     */
    public function index(): Response
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN');
        $categories = $this->category->findAll();
        return $this->render('categories/index.html.twig',[
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/categories/create", name="create-category")
     */
    public function create(Request $request)
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN');
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirect($this->generateUrl('categories'));
        }

        return $this->render('categories/create.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/categories/edit/{id}", name="edit-category")
     */
    public function edit($id, Request $request)
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN');
        $category = $this->category->find($id);
        $form = $this->createForm(CategoryType::class,$category);
        $form->handleRequest($request);

        if($form->isSubmitted())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirect($this->generateUrl('categories'));
        }

        return $this->render('categories/create.html.twig',[
            'form' => $form->createView()
        ]);

    }
    /**
     * @Route("/categories/remove/{id}", name="remove-category")
     */
    public function remove($id)
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN');
        $category = $this->category->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        return $this->redirect($this->generateUrl('categories'));
    }
    /**
     * @Route("/categories/show/{id}", name="show-category")
     */
    public function show($id)
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN');
        $categories = $this->category->findBooksByCategory($id);
        return $this->render('categories/show.html.twig',[
            'categories' => $categories
        ]);
    }
}
