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
    /**
     * @Route("/categories", name="categories")
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN');
        $categories = $categoryRepository->findAll();
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
    public function edit($id, CategoryRepository $categoryRepository, Request $request)
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN');
        $category = $categoryRepository->find($id);
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
    public function remove($id, CategoryRepository $categoryRepository)
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN');
        $category = $categoryRepository->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        return $this->redirect($this->generateUrl('categories'));
    }
    /**
     * @Route("/categories/show/{id}", name="show-category")
     */
    public function show($id, CategoryRepository $categoryRepository)
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN');
        $categories = $categoryRepository->findBooksByCategory($id);
        return $this->render('categories/show.html.twig',[
            'categories' => $categories
        ]);
    }
}
