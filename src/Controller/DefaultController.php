<?php

namespace App\Controller;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    // TODO : Ajouter la vérification du user ICI
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        if ($this->isGranted("ROLE_USER")) {
            return $this->render('default/index.html.twig', []);
        }
        return $this->redirectToRoute("login");
    }
    // TODO : Tests à réaliser sur la route
    // => Quand on est connecté, on doit être redirigé vers la page d'accueil
    // => Quand on n'est pas connecté, on doit être redirigé vers la page de connexion
}
