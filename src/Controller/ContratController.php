<?php

namespace App\Controller;

use App\Entity\Contrat;
use App\Form\ContratType;
use App\Repository\ContratRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/contrat')]
final class ContratController extends AbstractController
{
    #[Route(name: 'app_contrat_index', methods: ['GET'])]
    public function index(ContratRepository $contratRepository): Response
    {
        return $this->render('contrat/index.html.twig', [
            'contrats' => $contratRepository->findAll(),
        ]);
    }

    #[Route('/search', name: 'app_contrat_search', methods: ['GET'])]
    public function search(Request $request, ContratRepository $contratRepository): Response
    {
        $search = $request->query->get('search');
        $contrats = $contratRepository->searchByName($search);

        return $this->render('contrat/_contrats_table.html.twig', [
            'contrats' => $contrats,
        ]);
    }

    #[Route('/new', name: 'app_contrat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contrat = new Contrat();
        $form = $this->createForm(ContratType::class, $contrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contrat);
            $entityManager->flush();

            return $this->redirectToRoute('app_contrat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contrat/new.html.twig', [
            'contrat' => $contrat,
            'form' => $form,
        ]);
    }

    #[Route('/contrat/{id}', name: 'app_contrat_show')]
    public function show(Contrat $contrat): Response
    {
        return $this->render('contrat/show.html.twig', [
            'contrat' => $contrat,
        ]);
    }
   
    
    #[Route('/{id}/edit', name: 'app_contrat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contrat $contrat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ContratType::class, $contrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_contrat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contrat/edit.html.twig', [
            'contrat' => $contrat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_contrat_delete', methods: ['POST'])]
    public function delete(Request $request, Contrat $contrat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $contrat->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($contrat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_contrat_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/api/contrats-expirant-bientot', name: 'api_contrat_expirant_bientot', methods: ['GET'])]
    public function getExpiringContracts(ContratRepository $contratRepository): JsonResponse
    {
        $contratsExpirants = $contratRepository->findContractsExpiringSoon();

        $data = array_map(function ($contrat) {
            return [
                'id' => $contrat->getId(),
                'nomprenom' => $contrat->getNomprenom(),
                'dateFin' => $contrat->getDateFin()->format('Y-m-d'),
            ];
        }, $contratsExpirants);

        return new JsonResponse($data);
    }

    #[Route('/generate-pdf', name: 'app_contrat_generate_pdf', methods: ['GET'])]
public function generatePdf(ContratRepository $contratRepository): Response
{
    // Récupérer la liste des contrats
    $contrats = $contratRepository->findAll();

    // Générer le contenu HTML à partir de la liste des contrats
    $html = $this->renderView('contrat/pdf.html.twig', [
        'contrats' => $contrats,
    ]);

    // Initialiser Dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);
    $dompdf = new Dompdf($options);

    // Charger le HTML dans Dompdf
    $dompdf->loadHtml($html);

    // (Optional) Définir la taille du papier
    $dompdf->setPaper('A4', 'portrait');

    // Rendre le PDF
    $dompdf->render();

    // Générer un fichier PDF et l'envoyer au navigateur
    return new Response(
        $dompdf->output(),
        200,
        [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="contrats_list.pdf"',
        ]
    );
}

}
