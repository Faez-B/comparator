<?php

namespace App\Controller;

use App\Repository\VoitureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/comparison')]
class ComparisonController extends AbstractController
{
    #[Route('/voiture', name: 'voiture_comparison', methods: ['GET'])]
    public function compareVoitures(Request $request, VoitureRepository $voitureRepository): Response
    {
        $voitureIds = $request->query->all('ids');
        
        $voitures = [];
        if (!empty($voitureIds)) {
            foreach ($voitureIds as $id) {
                $voiture = $voitureRepository->find($id);
                if ($voiture) {
                    $voitures[] = $voiture;
                }
            }
        }

        // Limit to 3 vehicles for comparison
        $voitures = array_slice($voitures, 0, 3);

        return $this->render('comparison/voiture.html.twig', [
            'voitures' => $voitures,
            'allVoitures' => $voitureRepository->findAll(),
        ]);
    }

    #[Route('/voiture/select', name: 'voiture_comparison_select', methods: ['POST'])]
    public function selectVoituresForComparison(Request $request): Response
    {
        $selectedIds = $request->request->all('selected');
        
        if (empty($selectedIds)) {
            $this->addFlash('warning', 'Veuillez sélectionner au moins une voiture à comparer.');
            return $this->redirectToRoute('voiture_index');
        }

        return $this->redirectToRoute('voiture_comparison', [
            'ids' => $selectedIds
        ]);
    }
}
