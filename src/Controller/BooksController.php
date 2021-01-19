<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class BooksController extends AbstractController
{
    /**
     * @Route("/books", name="books")
     */
    public function index(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAll();
        return $this->render('books/index.html.twig', [
            'books' => $books,
        ]);
    }
    /**
     * @Route("/books/create", name="create-books")
     */
    public function create(Request $request, SluggerInterface $slugger)
    {
        $book = new Book();
        $form = $this->createForm(BookType::class,$book);
        $form->handleRequest($request);

        if($form->isSubmitted())
        {
            /** @var UploadedFile $file */
            $file = $form->get('imageFilename')->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                // Move the file to the directory where images are stored
                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $book->setImageFilename($newFilename);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($book);
            $em->flush();

            return $this->redirect($this->generateUrl('books'));
        }

        return $this->render('books/create.html.twig',[
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/books/edit/{id}", name="edit-books")
     */
    public function edit($id, Request $request, BookRepository $bookRepository, SluggerInterface $slugger)
    {
        $book = $bookRepository->find($id);
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if($form->isSubmitted())
        {
            /** @var UploadedFile $file */
            $file = $form->get('imageFilename')->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                // Move the file to the directory where images are stored
                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $book->setImageFilename($newFilename);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($book);
            $em->flush();

            return $this->redirect($this->generateUrl('books'));
        }
        return $this->render('books/create.html.twig',[
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/books/remove/{id}", name="remove-books")
     */
    public function remove($id, BookRepository $bookRepository)
    {
        $book = $bookRepository->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($book);
        $em->flush();

        return $this->redirect($this->generateUrl('books'));
    }
    /**
     * @Route("/books/show/{id}", name="show-books")
     */
    public function show($id, BookRepository $bookRepository)
    {
        $book = $bookRepository->find($id);
        return $this->render('books/show.create.twig',[
            'book' => $book
        ]);
    }
}
