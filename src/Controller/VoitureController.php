<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use App\Entity\Marque;
use App\Entity\Energie;
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
        $em = $this->getDoctrine()->getManager();
        $marques = $em->getRepository(Marque::class)->findAllAsc();
        $energies = $em->getRepository(Energie::class)->findAll();

        // $prixMax = $em->getRepository(Voiture::class)->getMaxPrix();
        $prixMax = $voitureRepository->getMaxPrix();

        return $this->render('voiture/index.html.twig', [
            'voitures' => $voitureRepository->findAll(),
            'marques' => $marques,
            'energies' => $energies,
            'prixMax' => $prixMax,
        ]);
    }

    #[Route('/search', name: 'app_voiture_search', methods: ["POST"])]
    public function search(Request $request): Response
    {
        $energie = null; $marque = null; $prixMax = null;

        $em = $this->getDoctrine()->getManager();

        if (isset($_POST["energie"]) && $_POST["energie"])  
            $energie = $em->getRepository(Energie::class)->find((int)$_POST["energie"]);

        if (isset($_POST["marque"]) && $_POST["marque"])    
            $marque = $em->getRepository(Marque::class)->find((int)$_POST["marque"]);

        if (isset($_POST["prixMax"]) && $_POST["prixMax"])  $prixMax = $_POST["prixMax"];

        // dd($energie, $prixMax, $marque);

        return $this->render('voiture/_index_body.html.twig', [
            'voitures' => $em->getRepository(Voiture::class)->search($marque, $energie, $prixMax),
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
        $energies = $em->getRepository(Energie::class)->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $voitureRepository->add($voiture, false);

            if ($_POST["marque"]) {
                $marque = $em->getRepository(Marque::class)->find($_POST["marque"]);
                $voiture->setMarque($marque);
            }

            if ($_POST['energie']) {
                $energie = $em->getRepository(Energie::class)->find($_POST['energie']);
                $voiture->setEnergie($energie);
            }

            $em->persist($voiture);
            $em->flush();

            return $this->redirectToRoute('app_voiture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('voiture/new.html.twig', [
            'voiture' => $voiture,
            'marques' => $marques,
            'energies' => $energies,
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
        $energies = $em->getRepository(Energie::class)->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $voitureRepository->add($voiture, false);
            
            if ($_POST["marque"]) {
                $marque = $em->getRepository(Marque::class)->find($_POST["marque"]);
                $voiture->setMarque($marque);
            }

            if ($_POST['energie']) {
                $energie = $em->getRepository(Energie::class)->find($_POST['energie']);
                $voiture->setEnergie($energie);
            }

            $em->persist($voiture);
            $em->flush();

            dd($voiture);

            return $this->redirectToRoute('app_voiture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('voiture/edit.html.twig', [
            'voiture' => $voiture,
            'marques' => $marques,
            'energies' => $energies,
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
