<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Grade;
use App\Entity\Status;
use App\Form\BookType;
use App\Form\GradeType;
use App\Form\StatusType;
use App\Repository\BookRepository;
use App\Repository\GradeRepository;
use App\Repository\StatusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class BooksController extends AbstractController
{
    private $books;
    public function __construct(BookRepository $bookRepository)
    {
        $this->books = $bookRepository;
    }
    /**
     * @Route("/books", name="books")
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $books = $this->books->findAll();
        return $this->render('books/index.html.twig', [
            'books' => $books,
        ]);
    }
    /**
     * @Route("/books/create", name="create-books")
     */
    public function create(Request $request, SluggerInterface $slugger)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
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
    public function edit($id, Request $request, SluggerInterface $slugger)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $book = $this->books->find($id);
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
    public function remove($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $book = $this->books->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($book);
        $em->flush();

        return $this->redirect($this->generateUrl('books'));
    }
    /**
     * @Route("/books/show/{id}", name="show-books")
     */
    public function show($id, Request $request, GradeRepository $gradeRepository, StatusRepository $statusRepository)
    {
        $book = $this->books->find($id);
        $user = $this->getUser();
        $form_status = $this->status($request,$book,$statusRepository,$user);
        $form_grade = $this->rate($request,$book,$gradeRepository,$user);
        return $this->render('books/show.create.twig',[
            'book' => $book,
            'form' => $form_grade->createView(),
            'form2' => $form_status->createView()
        ]);
    }
    /**
     * @Route("/grades/create", name="create-grades")
     */
    public function rate($request,$book,$gradeRepository,$user)
    {
        if($gradeRepository->findOneByBookIdAndUserId($book,$user))
            $grade = $gradeRepository->findOneByBookIdAndUserId($book,$user);
        else
            $grade = new Grade();

        $form = $this->createForm(GradeType::class,$grade);
        $form->handleRequest($request);
        if($form->isSubmitted())
        {
            $em = $this->getDoctrine()->getManager();
            $grade->setUserId($user);
            $grade->setBookId($book);
            $em->persist($grade);
            $em->flush();
        }
        return $form;
    }
    /**
     * @Route("/status/create", name="status-grades")
     */
    public function status($request,$book,$statusRepository,$user)
    {
        if($statusRepository->findOneByBookIdAndUserId($book, $user))
            $status = $statusRepository->findOneByBookIdAndUserId($book, $user);
        else
            $status = new Status();
        $form_status = $this->createForm(StatusType::class,$status);
        $form_status->handleRequest($request);
        if($form_status->isSubmitted())
        {
            $em = $this->getDoctrine()->getManager();
            $status->setBookId($book);
            $status->setUserId($user);
            $em->persist($status);
            $em->flush();
        }

        return $form_status;
    }
}
