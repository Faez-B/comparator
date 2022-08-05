<?php

namespace App\Controller;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        // throw new Exception('Hello Ignition!');
        if ($this->isGranted("ROLE_USER")) {
            return $this->render('default/index.html.twig', []);
        }
        return $this->redirectToRoute("login");
    }
}
