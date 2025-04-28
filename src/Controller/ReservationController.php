<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Trip;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reservation')]
final class ReservationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/', name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

    #[Route('/admin', name: 'app_reservation_admin', methods: ['GET'])]
    public function indexadmin(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/admin_reservation.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

    #[Route('/admin/{id}', name: 'app_reservation_admin_show', methods: ['GET'])]
    public function Adminshow(Reservation $reservation): Response
    {
        return $this->render('reservation/admin_show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/driver', name: 'app_reservation_index_driver', methods: ['GET'])]
    public function indexDriver(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index_driver.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

    #[Route('/new/{tripId}', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, int $tripId): Response
    {
        $trip = $this->entityManager->getRepository(Trip::class)->find($tripId);
        
        if (!$trip) {
            $this->addFlash('error', 'Trajet non trouvé');
            return $this->redirectToRoute('app_trip_index');
        }
    
        $reservation = new Reservation();
        $reservation->setTrip($trip);
        $reservation->setDateReservation(new \DateTime());
        $reservation->setEtatReservation('en_attente');
    
        $form = $this->createForm(ReservationType::class, $reservation, [
            'available_seats' => $trip->getAvailableSeats()
        ]);
        
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $nombrePlaces = $reservation->getNombrePlaces();
            
            if ($nombrePlaces <= 0 || $nombrePlaces > $trip->getAvailableSeats()) {
                $this->addFlash('error', 'Nombre de places invalide');
                return $this->redirectToRoute('app_reservation_new', ['tripId' => $tripId]);
            }
    
            $montantTotal = $trip->getContribution() * $nombrePlaces;
            $reservation->setMontantTotal($montantTotal);
            $trip->setAvailableSeats($trip->getAvailableSeats() - $nombrePlaces);
    
            try {
                $this->entityManager->persist($reservation);
                $this->entityManager->flush();
    
                $this->addFlash('success', 'Réservation effectuée avec succès !');
                return $this->redirectToRoute('app_reservation_show', ['id' => $reservation->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la réservation');
            }
        }
    
        return $this->render('reservation/new.html.twig', [
            'trip' => $trip,
            'form' => $form->createView(),
            'available_seats' => $trip->getAvailableSeats(),
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation): Response
    {
        $trip = $reservation->getTrip();
        $oldPlaces = $reservation->getNombrePlaces();

        $form = $this->createForm(ReservationType::class, $reservation, [
            'available_seats' => $trip->getAvailableSeats() + $oldPlaces
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPlaces = $reservation->getNombrePlaces();
            $difference = $oldPlaces - $newPlaces;

            $trip->setAvailableSeats($trip->getAvailableSeats() + $difference);
            $reservation->setMontantTotal($trip->getContribution() * $newPlaces);

            $this->entityManager->flush();

            $this->addFlash('success', 'Réservation modifiée avec succès');
            return $this->redirectToRoute('app_reservation_show', ['id' => $reservation->getId()]);
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $trip = $reservation->getTrip();
            $trip->setAvailableSeats($trip->getAvailableSeats() + $reservation->getNombrePlaces());

            $this->entityManager->remove($reservation);
            $this->entityManager->flush();

            $this->addFlash('success', 'Réservation annulée avec succès');
        } else {
            $this->addFlash('error', 'Token CSRF invalide');
        }

        return $this->redirectToRoute('app_reservation_index');
    }

    #[Route('/{id}/manage-driver', name: 'app_reservation_manage_driver', methods: ['GET', 'POST'])]
    public function manageDriver(Request $request, Reservation $reservation): Response
    {
        if ($request->isMethod('POST')) {
            $action = $request->request->get('action');
            
            if ($action === 'confirm') {
                $reservation->setEtatReservation('confirmé');
                $this->addFlash('success', 'Réservation confirmée avec succès');
            } elseif ($action === 'reject') {
                $reservation->setEtatReservation('refusé');
                $trip = $reservation->getTrip();
                $trip->setAvailableSeats($trip->getAvailableSeats() + $reservation->getNombrePlaces());
                $this->addFlash('warning', 'Réservation refusée');
            }
            
            $this->entityManager->flush();
            return $this->redirectToRoute('app_reservation_manage_driver', ['id' => $reservation->getId()]);
        }
    
        return $this->render('reservation/manage_driver.html.twig', [
            'reservation' => $reservation,
        ]);
    }
}