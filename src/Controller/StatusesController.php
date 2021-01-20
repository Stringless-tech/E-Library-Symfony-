<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatusesController extends AbstractController
{
    /**
     * @Route("/statuses", name="statuses")
     */
    public function index(): Response
    {
        return $this->render('statuses/index.html.twig', [
            'controller_name' => 'StatusesController',
        ]);
    }
}
