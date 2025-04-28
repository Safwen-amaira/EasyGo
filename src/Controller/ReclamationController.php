<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Snipe\BanBuilder\CensorWords;
use Knp\Component\Pager\PaginatorInterface;
#[Route('/reclamation')]
final class ReclamationController extends AbstractController
{
    #[Route('/', name: 'app_reclamation_index', methods: ['GET'])]
public function index(
    ReclamationRepository $reclamationRepository,
    Request $request,
    PaginatorInterface $paginator
): Response {
    // Recherche
    $searchTerm = $request->query->get('search', '');

    // Tri (par défaut par date de création)
    $sortField = $request->query->get('sortField', 'dateCreation');
    $sortDirection = $request->query->get('sortDirection', 'DESC');

    // Construire un QueryBuilder pour la recherche et le tri
    $qb = $reclamationRepository->createQueryBuilder('r')
        ->orderBy('r.' . $sortField, $sortDirection);

    if ($searchTerm) {
        $qb->andWhere('r.description LIKE :searchTerm')
           ->setParameter('searchTerm', '%' . $searchTerm . '%');
    }

    // Pagination
    $page = $request->query->getInt('page', 1);
    $limit = 3; // nombre d'éléments par page

    $pagination = $paginator->paginate(
        $qb,
        $page,
        $limit
    );

    // Envoi des résultats à Twig
    return $this->render('reclamation/index.html.twig', [
        'pagination' => $pagination,
        'search' => $searchTerm,
        'sortField' => $sortField,
        'sortDirection' => $sortDirection,
    ]);
}

    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();
        $censor = new CensorWords;
        $langs = array('fr', 'it', 'en-us', 'en-uk', 'es');
        $badwords = $censor->setDictionary($langs);
        $censor->setReplaceChar("*");
        // Définir la date de création à la date actuelle
        $reclamation->setDateCreation(new \DateTime());
    
        // Définir le statut par défaut à "en attente"
        $reclamation->setStatut('en attente');
    
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $string = $censor->censorString($reclamation->getDescription());
            $reclamation->setDescription($string['clean']);
   
            $entityManager->persist($reclamation);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }
    

    #[Route('/{id}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/reclamation/stats', name: 'app_reclamation_stats', methods: ['GET'])]
    public function stats(ReclamationRepository $repo): Response
    {
        // 1) On récupère les stats brutes depuis la BDD
        $rawStats = $repo->createQueryBuilder('r')
            ->select('r.statut AS statut, COUNT(r.id) AS count')
            ->groupBy('r.statut')
            ->getQuery()
            ->getResult();

        // 2) On prépare deux tableaux : labels (statuts) et valeurs (nombre)
        $labels = [];
        $data   = [];
        foreach ($rawStats as $row) {
            $labels[] = $row['statut'];
            $data[]   = (int) $row['count'];
        }

        // 3) On construit la configuration Chart.js en PHP
        $chartConfig = [
            'type'    => 'pie',
            'data'    => [
                'labels'   => $labels,
                'datasets' => [[
                    'data' => $data,
                ]]
            ],
            'options' => [
                'plugins' => [
                    'legend' => ['position' => 'bottom']
                ]
            ]
        ];

        // 4) On crée l’URL QuickChart avec la config encodée
        $encodedConfig = urlencode(json_encode($chartConfig));
        $chartUrl      = "https://quickchart.io/chart?c={$encodedConfig}&width=600&height=400";

        return $this->render('reclamation/stats.html.twig', [
            'chartUrl' => $chartUrl,
        ]);
    }
}
