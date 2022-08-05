<?php

namespace App\Controller;

use App\Entity\Energie;
use App\Form\EnergieType;
use App\Repository\EnergieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/energie')]
class EnergieController extends AbstractController
{
    #[Route('/', name: 'app_energie_index', methods: ['GET'])]
    public function index(EnergieRepository $energieRepository): Response
    {
        return $this->render('energie/index.html.twig', [
            'energies' => $energieRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_energie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EnergieRepository $energieRepository): Response
    {
        $energie = new Energie();
        $form = $this->createForm(EnergieType::class, $energie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $energieRepository->add($energie, true);

            return $this->redirectToRoute('app_energie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('energie/new.html.twig', [
            'energie' => $energie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_energie_show', methods: ['GET'])]
    public function show(Energie $energie): Response
    {
        return $this->render('energie/show.html.twig', [
            'energie' => $energie,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_energie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Energie $energie, EnergieRepository $energieRepository): Response
    {
        $form = $this->createForm(EnergieType::class, $energie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $energieRepository->add($energie, true);

            return $this->redirectToRoute('app_energie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('energie/edit.html.twig', [
            'energie' => $energie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_energie_delete', methods: ['POST'])]
    public function delete(Request $request, Energie $energie, EnergieRepository $energieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$energie->getId(), $request->request->get('_token'))) {
            $energieRepository->remove($energie, true);
        }

        return $this->redirectToRoute('app_energie_index', [], Response::HTTP_SEE_OTHER);
    }
}
