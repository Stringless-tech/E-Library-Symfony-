<?php

namespace App\Controller;

use App\Entity\Grade;
use App\Form\GradeType;
use App\Repository\GradeRepository;
use Container8hKDx5x\getGradeRepositoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GradesController extends AbstractController
{
    /**
     * @Route("/grades", name="grades")
     */
    public function index(): Response
    {
        return $this->render('grades/index.html.twig', [
            'controller_name' => 'GradesController',
        ]);
    }
    /**
     * @Route("/grades/create", name="create-grades")
     */
    public function create(Request $request)
    {
        $grade = new Grade();
        $form = $this->createForm(GradeType::class,$grade);
        $form->handleRequest($request);
        if($form->isSubmitted())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($grade);
            $em->flush();

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('grades/create.html.twig',[
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/grades/edit", name="edit-grades")
     */
    public function edit(Request $request,$id,GradeRepository $gradeRepository)
    {
        $grade = $gradeRepository->find($id);
        $form = $this->createForm(GradeType::class,$grade);
        $form->handleRequest($request);
        if($form->isSubmitted())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($grade);
            $em->flush();

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('grades/create.html.twig',[
            'form' => $form->createView()
        ]);
    }
}
