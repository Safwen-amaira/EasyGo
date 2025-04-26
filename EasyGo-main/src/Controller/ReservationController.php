<?php

namespace App\Controller;

use App\Entity\Reservation;
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
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Knp\Component\Pager\PaginatorInterface;
use App\Service\InfobipService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Psr\Log\LoggerInterface; 



#[Route('/reservation')]
final class ReservationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }
    
    #[Route('/', name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $reservationRepository->createQueryBuilder('r')
            ->orderBy('r.dateReservation', 'DESC')
            ->getQuery();
    
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), // Numéro de page par défaut
            10 // Nombre d'éléments par page
        );
    
        return $this->render('reservation/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
    #[Route('/admin', name: 'app_reservation_admin', methods: ['GET'])]
    public function indexadmin(ReservationRepository $reservationRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $reservationRepository->createQueryBuilder('r')
            ->orderBy('r.dateReservation', 'DESC')
            ->getQuery();
    
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), // Numéro de page par défaut
            10 // Nombre d'éléments par page
        );
    
        return $this->render('reservation/admin_reservation.html.twig', [
            'pagination' => $pagination,
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
    public function indexDriver(ReservationRepository $reservationRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $reservationRepository->createQueryBuilder('r')
            ->orderBy('r.dateReservation', 'DESC')
            ->getQuery();
    
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), // Numéro de page par défaut
            10 // Nombre d'éléments par page
        );
    
        return $this->render('reservation/index_driver.html.twig', [
            'pagination' => $pagination,
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
            // On ne décrémente pas encore les places disponibles, seulement à la confirmation
            $reservation->setUser($this->getUser());
    
            try {
                $this->entityManager->persist($reservation);
                $this->entityManager->flush();
    
                $this->addFlash('success', 'Réservation effectuée avec succès ! En attente de confirmation du conducteur.');
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
            'available_seats' => $trip->getAvailableSeats() + ($reservation->getEtatReservation() === 'confirmé' ? $oldPlaces : 0)
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPlaces = $reservation->getNombrePlaces();
            
            if ($reservation->getEtatReservation() === 'confirmé') {
                $difference = $oldPlaces - $newPlaces;
                $trip->setAvailableSeats($trip->getAvailableSeats() + $difference);
            }
            
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
            
            // On remet les places disponibles seulement si la réservation était confirmée
            if ($reservation->getEtatReservation() === 'confirmé') {
                $trip->setAvailableSeats($trip->getAvailableSeats() + $reservation->getNombrePlaces());
            }

            $this->entityManager->remove($reservation);
            $this->entityManager->flush();

            $this->addFlash('success', 'Réservation annulée avec succès');
        } else {
            $this->addFlash('error', 'Token CSRF invalide');
        }

        return $this->redirectToRoute('app_reservation_index');
    }
    
    #[Route('/{id}/manage-driver', name: 'app_reservation_manage_driver', methods: ['GET', 'POST'])]
    public function manageDriver(
        Request $request, 
        Reservation $reservation,
        InfobipService $infobipService,
        ParameterBagInterface $params,
        LoggerInterface $logger
    ): Response {
        $logger->info('Entering manageDriver', ['reservation' => $reservation->getId()]);
        
        if ($request->isMethod('POST')) {
            $action = $request->request->get('action');
            $logger->info('Action received', ['action' => $action]);
            $trip = $reservation->getTrip();
            
            if ($action === 'confirm') {
                $logger->debug('Confirming reservation');
                
                if ($reservation->getNombrePlaces() > $trip->getAvailableSeats()) {
                    $this->addFlash('error', 'Pas assez de places disponibles pour confirmer cette réservation');
                    return $this->redirectToRoute('app_reservation_manage_driver', ['id' => $reservation->getId()]);
                }
                
                $reservation->setEtatReservation('confirmé');
                $trip->setAvailableSeats($trip->getAvailableSeats() - $reservation->getNombrePlaces());
                $this->addFlash('success', 'Réservation confirmée avec succès');
                
                // Envoi du SMS modifié
                $user = $reservation->getUser();
                $phoneNumber = $user?->getPhoneNumber() ?? $params->get('test_phone_number');
                if (!empty($phoneNumber)) {
                    // Ajoutez ce logging
                    $logger->info('Phone number to use', [
                        'phone' => $phoneNumber,
                        'source' => $user ? 'user profile' : 'test number'
                    ]);
                    
                    $message = sprintf(
                        "EasyGo - Confirmation de réservation\n\n" .
                        "Votre réservation N°%d est confirmée :\n" .
                        "Départ: %s\n" .
                        "Arrivée: %s\n" .
                        "Date: %s\n" .
                        "Détails: %d place(s) - %.2f DNT\n\n" .
                        "Merci de voyager avec EasyGo!",
                        $reservation->getId(),
                        $trip->getDeparturePoint(),
                        $trip->getDestination(),
                        $trip->getTripDate()->format('d/m/Y H:i'),
                        $reservation->getNombrePlaces(),
                        $reservation->getMontantTotal()
                    );

                    $logger->debug('Attempting to send SMS', [
                        'phone' => $phoneNumber,
                        'message' => $message
                    ]);
                    
                    try {
                        $smsSent = $infobipService->sendSms($phoneNumber, $message);
                        
                        if ($smsSent) {
                            $logger->info('SMS successfully sent');
                            $this->addFlash('success', 'SMS de confirmation envoyé au passager');
                        } else {
                            $logger->error('SMS sending failed (no exception)');
                            $this->addFlash('warning', 'Réservation confirmée mais échec d\'envoi du SMS');
                        }
                    } catch (\Exception $e) {
                        $logger->error('SMS sending error', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        $this->addFlash('warning', 'Réservation confirmée mais erreur lors de l\'envoi du SMS');
                    }
                } else {
                    $logger->warning('No phone number available for SMS', [
                        'user_id' => $user?->getId(),
                        'has_phone' => !empty($phoneNumber)
                    ]);
                    $this->addFlash('warning', 'Réservation confirmée mais SMS non envoyé (numéro non disponible)');
                }
            } elseif ($action === 'reject') {
                $logger->debug('Rejecting reservation');
                $reservation->setEtatReservation('refusé');
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
    
