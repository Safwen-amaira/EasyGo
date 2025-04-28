<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Form\TripType;
use App\Repository\TripRepository;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/trip')]
final class TripController extends AbstractController
{
    #[Route('/statistics', name: 'app_trip_statistics')]
    public function statistics(ReservationRepository $reservationRepo, TripRepository $tripRepo): Response
    {
        // Statistiques des réservations
        $reservationStats = [
            'total' => $reservationRepo->createQueryBuilder('r')
                ->select('COUNT(r.id)')
                ->getQuery()
                ->getSingleScalarResult(),
            'confirmed' => $reservationRepo->count(['etatReservation' => 'confirmé']),
            'pending' => $reservationRepo->count(['etatReservation' => 'en_attente']),
            'rejected' => $reservationRepo->count(['etatReservation' => 'refusé']),
            'monthly' => $reservationRepo->getMonthlyReservations(),
        ];
    
        // Statistiques des trajets
        $tripStats = [
            'total' => $tripRepo->count([]),
            'active' => $tripRepo->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->where('t.trip_date >= :today')
                ->setParameter('today', new \DateTime())
                ->getQuery()
                ->getSingleScalarResult(),
            'completed' => $tripRepo->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->where('t.trip_date < :today')
                ->setParameter('today', new \DateTime())
                ->getQuery()
                ->getSingleScalarResult(),
            'popular_destinations' => $tripRepo->getPopularDestinations(5),
        ];
    
        // Revenus totaux
        $totalRevenue = $reservationRepo->createQueryBuilder('r')
            ->select('SUM(r.montantTotal)')
            ->where('r.etatReservation = :confirmed')
            ->setParameter('confirmed', 'confirmé')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    
        // Graphique des réservations par statut
        $reservationStatusChart = $this->createChart(
            'pie',
            ['Confirmées', 'En attente', 'Refusées'],
            [$reservationStats['confirmed'], $reservationStats['pending'], $reservationStats['rejected']],
            'Statut des réservations',
            ['#4e73df', '#1cc88a', '#e74a3b']
        );
    
        // Graphique des trajets
        $tripStatusChart = $this->createChart(
            'doughnut',
            ['À venir', 'Effectués'],
            [$tripStats['active'], $tripStats['completed']],
            'Statut des trajets',
            ['#36b9cc', '#f6c23e']
        );
    
        // Graphique des réservations mensuelles
        $months = array_map(fn($m) => $this->getMonthName($m['month']), $reservationStats['monthly']);
        $counts = array_column($reservationStats['monthly'], 'count');
        $monthlyRevenueChart = $this->createChart(
            'bar',
            $months,
            $counts,
            'Réservations par mois',
            ['#4e73df']
        );
    
        return $this->render('trip/statistics.html.twig', [
            'reservation_stats' => $reservationStats,
            'trip_stats' => $tripStats,
            'total_revenue' => $totalRevenue,
            'reservation_chart' => $reservationStatusChart,
            'trip_chart' => $tripStatusChart,
            'monthly_chart' => $monthlyRevenueChart,
        ]);
    }
    
    private function createChart(string $type, array $labels, array $data, string $title, array $colors): string
    {
        $chartConfig = [
            'type' => $type,
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => '#fff',
                    'borderWidth' => 1,
                ]]
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'legend' => [
                        'position' => 'bottom',
                        'labels' => [
                            'font' => ['size' => 12],
                            'padding' => 20
                        ]
                    ],
                    'title' => [
                        'display' => true,
                        'text' => $title,
                        'font' => ['size' => 16]
                    ]
                ],
                'maintainAspectRatio' => false
            ]
        ];
    
        $encodedConfig = urlencode(json_encode($chartConfig));
        return "https://quickchart.io/chart?c={$encodedConfig}&width=600&height=300&backgroundColor=white";
    }
    
    private function getMonthName(int $monthNumber): string
    {
        $months = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];
        return $months[$monthNumber] ?? '';
    }

    #[Route('/home', name: 'app_trip_home')]
    public function home(Request $request, TripRepository $tripRepository): Response
    {
        $searchTerm = $request->query->get('search');
        $order = $request->query->get('order', 'ASC'); // Par défaut ASC si non spécifié
        
        if ($searchTerm) {
            $trips = $tripRepository->createQueryBuilder('t')
                ->where('LOWER(t.departure_point) LIKE LOWER(:search) OR LOWER(t.destination) LIKE LOWER(:search)')
                ->setParameter('search', '%'.$searchTerm.'%')
                ->orderBy('t.trip_date', $order)
                ->getQuery()
                ->getResult();
        } else {
            $trips = $tripRepository->findBy([], ['trip_date' => $order]);
        }
    
        return $this->render('trip/home.html.twig', [
            'trips' => $trips,
            'current_order' => $order, // On passe l'ordre courant au template
        ]);
    }
#[Route('/home_client', name: 'app_trip_home_client')]
public function homeclient(Request $request, TripRepository $tripRepository): Response
{
    $searchTerm = $request->query->get('search');
    $order = $request->query->get('order', 'ASC'); // Par défaut ASC si non spécifié
    
    if ($searchTerm) {
        $trips = $tripRepository->createQueryBuilder('t')
            ->where('LOWER(t.departure_point) LIKE LOWER(:search) OR LOWER(t.destination) LIKE LOWER(:search)')
            ->setParameter('search', '%'.$searchTerm.'%')
            ->orderBy('t.trip_date', $order)
            ->getQuery()
            ->getResult();
    } else {
        $trips = $tripRepository->findBy([], ['trip_date' => $order]);
    }

    return $this->render('trip/home_client.html.twig', [
        'trips' => $trips,
        'current_order' => $order, // On passe l'ordre courant au template
    ]);
}
#[Route('/home_admin', name: 'app_trip_home_admin')]
public function homeadmin(Request $request, TripRepository $tripRepository): Response
{
    $searchTerm = $request->query->get('search');
    $order = $request->query->get('order', 'ASC'); // Par défaut ASC si non spécifié
    
    if ($searchTerm) {
        $trips = $tripRepository->createQueryBuilder('t')
            ->where('LOWER(t.departure_point) LIKE LOWER(:search) OR LOWER(t.destination) LIKE LOWER(:search)')
            ->setParameter('search', '%'.$searchTerm.'%')
            ->orderBy('t.trip_date', $order)
            ->getQuery()
            ->getResult();
    } else {
        $trips = $tripRepository->findBy([], ['trip_date' => $order]);
    }

    return $this->render('trip/home_admin.html.twig', [
        'trips' => $trips,
        'current_order' => $order, // On passe l'ordre courant au template
    ]);
}

#[Route('/', name: 'app_trip_index', methods: ['GET', 'POST'])]
public function index(TripRepository $tripRepository, PaginatorInterface $paginator, Request $request): Response
{
    $searchCriteria = [
        'departurePoint' => $request->query->get('departurePoint'),
        'destination' => $request->query->get('destination'),
        'tripDate' => $request->query->get('tripDate'), // Un seul champ de date maintenant
        'sort' => $request->query->get('sort', 't.trip_date'),
        'direction' => $request->query->get('direction', 'DESC'),
    ];

    $query = $tripRepository->createQueryBuilder('t');

    // Apply filters
    if ($searchCriteria['departurePoint']) {
        $query->andWhere('t.departure_point LIKE :departurePoint')
              ->setParameter('departurePoint', '%'.$searchCriteria['departurePoint'].'%');
    }

    if ($searchCriteria['destination']) {
        $query->andWhere('t.destination LIKE :destination')
              ->setParameter('destination', '%'.$searchCriteria['destination'].'%');
    }

    if ($searchCriteria['tripDate']) {
        try {
            $tripDate = new \DateTime($searchCriteria['tripDate']);
            $query->andWhere('t.trip_date = :tripDate')
                  ->setParameter('tripDate', $tripDate);
        } catch (\Exception $e) {
            // Gérer l'erreur si nécessaire
        }
    }

    // Tri
    $query->orderBy($searchCriteria['sort'], $searchCriteria['direction']);

    $pagination = $paginator->paginate(
        $query->getQuery(),
        $request->query->getInt('page', 1),
        10
    );

    if ($request->isXmlHttpRequest()) {
        return $this->render('trip/_trips_table.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    return $this->render('trip/index.html.twig', [
        'pagination' => $pagination,
        'searchCriteria' => $searchCriteria,
    ]);
}
#[Route('/admin_trip', name: 'app_trip_admin_trip', methods: ['GET'])]
public function indexadmintrip(TripRepository $tripRepository, PaginatorInterface $paginator, Request $request): Response
{
    // Initialisez searchCriteria avec les valeurs de la requête
    $searchCriteria = [
        'departurePoint' => $request->query->get('departurePoint', ''),
        'destination' => $request->query->get('destination', ''),
        'tripDate' => $request->query->get('tripDate', ''),
    ];

    $queryBuilder = $tripRepository->createQueryBuilder('t')
        ->orderBy('t.trip_date', 'DESC');

    // Ajoutez les conditions de recherche si elles existent
    if ($searchCriteria['departurePoint']) {
        $queryBuilder->andWhere('t.departure_point LIKE :departurePoint')
            ->setParameter('departurePoint', '%'.$searchCriteria['departurePoint'].'%');
    }

    if ($searchCriteria['destination']) {
        $queryBuilder->andWhere('t.destination LIKE :destination')
            ->setParameter('destination', '%'.$searchCriteria['destination'].'%');
    }

    if ($searchCriteria['tripDate']) {
        try {
            $tripDate = new \DateTime($searchCriteria['tripDate']);
            $queryBuilder->andWhere('t.trip_date = :tripDate')
                ->setParameter('tripDate', $tripDate->format('Y-m-d'));
        } catch (\Exception $e) {
            // Gérer l'erreur si nécessaire
        }
    }

    $pagination = $paginator->paginate(
        $queryBuilder->getQuery(),
        $request->query->getInt('page', 1),
        10
    );

    return $this->render('trip/admin_trip.html.twig', [
        'pagination' => $pagination,
        'searchCriteria' => $searchCriteria // N'oubliez pas de passer cette variable
    ]);
}

    #[Route('/publication_admin/{id}', name: 'app_trip_publication_admin', methods: ['GET'])]
    public function showadmin(Trip $trip): Response
    {
        return $this->render('trip/publication_admin.html.twig', [
            'trip' => $trip,
        ]);
    }

    #[Route('/new', name: 'app_trip_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $trip = new Trip();
        $form = $this->createForm(TripType::class, $trip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($trip);
            $entityManager->flush();

            return $this->redirectToRoute('app_trip_publication', ['id' => $trip->getId()]);
        }

        return $this->render('trip/new.html.twig', [
            'trip' => $trip,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_trip_show', methods: ['GET'])]
    public function show(Trip $trip): Response
    {
        return $this->render('trip/show.html.twig', [
            'trip' => $trip,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_trip_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trip $trip, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TripType::class, $trip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_trip_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trip/edit.html.twig', [
            'trip' => $trip,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_trip_delete', methods: ['POST'])]
    public function delete(Request $request, Trip $trip, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trip->getId(), $request->request->get('_token'))) {
            $entityManager->remove($trip);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_trip_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/publication', name: 'app_trip_publication', methods: ['GET'])]
    public function publication(Trip $trip): Response
    {
        return $this->render('trip/publication.html.twig', [
            'trip' => $trip,
        ]);
    }

    #[Route('/client/{id}', name: 'app_trip_client_view', methods: ['GET'])]
    public function clientView(Trip $trip): Response
    {
        return $this->render('trip/client_view.html.twig', [
            'trip' => $trip,
        ]);
    }

    #[Route('/trip/client', name: 'app_trip_client')]
    public function clientList(Request $request, TripRepository $tripRepository, PaginatorInterface $paginator): Response
    {
        $query = $tripRepository->createQueryBuilder('t')
            ->orderBy('t.tripDate', 'ASC')
            ->getQuery();

        $trips = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('trip/index.html.twig', [
            'trips' => $trips,
        ]);
    }

}
