<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Form\TripType;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/trip')]
final class TripController extends AbstractController
{

    #[Route('/home', name: 'app_trip_home')]
public function home(TripRepository $tripRepository): Response
{
    $trips = $tripRepository->findAll();

    return $this->render('trip/home.html.twig', [
        'trips' => $trips,
    ]);
}
#[Route('/home_client', name: 'app_trip_home_client')]
public function homeclient(Request $request, TripRepository $tripRepository): Response
{
    $searchTerm = $request->query->get('search');
    
    if ($searchTerm) {
        $trips = $tripRepository->createQueryBuilder('t')
            ->where('LOWER(t.departure_point) LIKE LOWER(:search) OR LOWER(t.destination) LIKE LOWER(:search)')
            ->setParameter('search', '%'.$searchTerm.'%')
            ->orderBy('t.trip_date', 'ASC')
            ->getQuery()
            ->getResult();
    } else {
        $trips = $tripRepository->findBy([], ['trip_date' => 'ASC']);
    }

    return $this->render('trip/home_client.html.twig', [
        'trips' => $trips,
    ]);
}
#[Route('/home_admin', name: 'app_trip_home_admin')]
public function homeadmin(TripRepository $tripRepository): Response
{
    $trips = $tripRepository->findAll();

    return $this->render('trip/home_admin.html.twig', [
        'trips' => $trips,
    ]);
}

    #[Route('/', name: 'app_trip_index', methods: ['GET'])]
    public function index(TripRepository $tripRepository): Response
    {
        return $this->render('trip/index.html.twig', [
            'trips' => $tripRepository->findAll(),
        ]);
    }

    #[Route('/admin_trip', name: 'app_trip_admin_trip', methods: ['GET'])]
    public function indexadmintrip(TripRepository $tripRepository): Response
    {
        return $this->render('trip/admin_trip.html.twig', [
            'trips' => $tripRepository->findAll(),
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