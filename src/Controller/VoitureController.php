<?php

namespace App\Controller;

use App\Entity\Marque;
use App\Entity\Energie;
use App\Entity\Voiture;
use App\Form\VoitureType;
use App\Repository\VoitureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/voiture')]
class VoitureController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route('/', name: 'voiture_index', methods: ['GET'])]
    public function index(VoitureRepository $voitureRepository): Response
    {
        $marques = $this->em->getRepository(Marque::class)->findAllAsc();
        $energies = $this->em->getRepository(Energie::class)->findAll();

        // $prixMax = $this->em->getRepository(Voiture::class)->getMaxPrix();
        $prixMax = $voitureRepository->getMaxPrix();

        return $this->render('voiture/index.html.twig', [
            'voitures' => $voitureRepository->findAll(),
            'marques' => $marques,
            'energies' => $energies,
            'prixMax' => $prixMax,
        ]);
    }
    // TODO : Tests à réaliser sur la route
    // => Vérifier qu'on récupère bien le prix maximum des voitures (le prix le plus élevé)

    #[Route('/search', name: 'voiture_search', methods: ["POST"])]
    public function search(Request $request): Response
    {
        $energie = null;
        $marque = null;
        $prixMax = null;
        $etat = null;
        $conso = null;
        $sortType = null;

        if (isset($_POST["energie"]) && $_POST["energie"]) {
            $energie = $this->em->getRepository(Energie::class)->find((int)$_POST["energie"]);
        }

        if (isset($_POST["marque"]) && $_POST["marque"]) {
            $marque = $this->em->getRepository(Marque::class)->find((int)$_POST["marque"]);
        }

        if (isset($_POST["prixMax"]) && $_POST["prixMax"]) {
            $prixMax = $_POST["prixMax"];
        }

        if (isset($_POST["etat"]) && $_POST["etat"]) {
            $etat = $_POST["etat"];
        }

        if (isset($_POST["conso"]) && $_POST["conso"]) {
            $conso = $_POST["conso"];
        }

        if (isset($_POST["sortType"]) && $_POST["sortType"]) {
            $sortType = $_POST["sortType"];
        }

        return $this->render('voiture/_index_body.html.twig', [
            'voitures' => $this->em->getRepository(Voiture::class)->search($marque, $energie, $prixMax, $etat, $conso, $sortType),
        ]);
    }
    // TODO : Tests à réaliser sur la route
    // => 1 - Vérifier qu'on récupère bien les voitures en fonction des critères de recherche
    // => 2 - Vérifier qu'on récupère bien les voitures en fonction des critères de recherche et qu'on les trie par le choix du trie
    // 3 - Vérifier que c'est bien une requête AJAX

    #[Route('/new', name: 'voiture_new', methods: ['GET', 'POST'])]
    public function new(Request $request, VoitureRepository $voitureRepository): Response
    {
        $voiture = new Voiture();
        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);

        $marques = $this->em->getRepository(Marque::class)->findAllAsc();
        $energies = $this->em->getRepository(Energie::class)->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $voitureRepository->add($voiture, false);

            // !!! Il faut que la marque, l'état et l'énergie soient obligatoires dans le formulaire !!!
            // Mettre en place un TEST pour vérifier que la marque et l'énergie sont bien renseignées

            // Enlever les if pour les champs
            // => il doivent être gérer dans le formulaire (Form)
            if ($_POST["marque"]) {
                $marque = $this->em->getRepository(Marque::class)->find($_POST["marque"]);
                $voiture->setMarque($marque);
            }

            if ($_POST['energie']) {
                $energie = $this->em->getRepository(Energie::class)->find($_POST['energie']);
                $voiture->setEnergie($energie);
            }

            if (isset($_POST['etat'])) {
                $voiture->setEtat($_POST['etat']);
            }

            if (isset($_POST['annee']) && $_POST['annee']) {
                $voiture->setAnnee($_POST['annee']);
            }

            if (isset($_POST['kilometrage']) && $_POST['kilometrage']) {
                $voiture->setKilometrage($_POST['kilometrage']);
            }

            $this->em->persist($voiture);
            $this->em->flush();

            return $this->redirectToRoute('voiture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('voiture/new.html.twig', [
            'voiture' => $voiture,
            'marques' => $marques,
            'energies' => $energies,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'voiture_show', methods: ['GET'])]
    public function show(Voiture $voiture): Response
    {
        return $this->render('voiture/show.html.twig', [
            'voiture' => $voiture,
        ]);
    }

    #[Route('/{id}/edit', name: 'voiture_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Voiture $voiture, VoitureRepository $voitureRepository): Response
    {
        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);

        $marques = $this->em->getRepository(Marque::class)->findAllAsc();
        $energies = $this->em->getRepository(Energie::class)->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $voitureRepository->add($voiture, false);

            // Enlever les if pour les champs
            // => il doivent être gérer dans le formulaire (Form)
            if ($_POST["marque"]) {
                $marque = $this->em->getRepository(Marque::class)->find($_POST["marque"]);
                $voiture->setMarque($marque);
            }

            if ($_POST['energie']) {
                $energie = $this->em->getRepository(Energie::class)->find($_POST['energie']);
                $voiture->setEnergie($energie);
            }

            if (isset($_POST['etat'])) {
                $voiture->setEtat($_POST['etat']);
            }

            $this->em->persist($voiture);
            $this->em->flush();

            // dd($voiture);

            return $this->redirectToRoute('voiture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('voiture/edit.html.twig', [
            'voiture' => $voiture,
            'marques' => $marques,
            'energies' => $energies,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'voiture_delete', methods: ['POST'])]
    public function delete(Request $request, Voiture $voiture, VoitureRepository $voitureRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$voiture->getId(), $request->request->get('_token'))) {
            $voitureRepository->remove($voiture, true);
        }

        return $this->redirectToRoute('voiture_index', [], Response::HTTP_SEE_OTHER);
    }
}
