<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use App\Entity\Marque;
use App\Entity\Voiture;
use App\Form\VoitureType;
use App\Repository\VoitureRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/voiture')]
class VoitureController extends AbstractController
{
    #[Route('/', name: 'app_voiture_index', methods: ['GET'])]
    public function index(VoitureRepository $voitureRepository): Response
    {
        return $this->render('voiture/index.html.twig', [
            'voitures' => $voitureRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_voiture_new', methods: ['GET', 'POST'])]
    public function new(Request $request, VoitureRepository $voitureRepository): Response
    {
        $voiture = new Voiture();
        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        $marques = $em->getRepository(Marque::class)->findAllAsc();

        if ($form->isSubmitted() && $form->isValid()) {
            $voitureRepository->add($voiture, false);

            if ($_POST["marque"]) {
                $marque = $em->getRepository(Marque::class)->find($_POST["marque"]);
                $voiture->setMarque($marque);
            }

            $em->persist($voiture);
            $em->flush();

            return $this->redirectToRoute('app_voiture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('voiture/new.html.twig', [
            'voiture' => $voiture,
            'marques' => $marques,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_voiture_show', methods: ['GET'])]
    public function show(Voiture $voiture): Response
    {
        return $this->render('voiture/show.html.twig', [
            'voiture' => $voiture,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_voiture_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Voiture $voiture, VoitureRepository $voitureRepository): Response
    {
        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        $marques = $em->getRepository(Marque::class)->findAllAsc();

        if ($form->isSubmitted() && $form->isValid()) {
            $voitureRepository->add($voiture, false);
            
            if ($_POST["marque"]) {
                $marque = $em->getRepository(Marque::class)->find($_POST["marque"]);
                $voiture->setMarque($marque);
            }

            $em->persist($voiture);
            $em->flush();

            dd($voiture);

            return $this->redirectToRoute('app_voiture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('voiture/edit.html.twig', [
            'voiture' => $voiture,
            'marques' => $marques,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_voiture_delete', methods: ['POST'])]
    public function delete(Request $request, Voiture $voiture, VoitureRepository $voitureRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$voiture->getId(), $request->request->get('_token'))) {
            $voitureRepository->remove($voiture, true);
        }

        return $this->redirectToRoute('app_voiture_index', [], Response::HTTP_SEE_OTHER);
    }
}
