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
use App\Service\TwilioSmsService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Psr\Log\LoggerInterface; 



#[Route('/reservation')]
final class ReservationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }
    #[Route('/reservation/{id}/validate', name: 'app_reservation_validate')]
public function validateReservation(Reservation $reservation): Response
{
    $user = $reservation->getUser();
    
    return $this->render('reservation/validate.html.twig', [
        'reservation' => $reservation,
        'passenger_name' => $user ? $user->getPrenom().' '.$user->getNom() : 'Inconnu',
        'passenger_phone' => $user ? ($user->getPhoneNumber() ?? 'Non renseigné') : 'Non renseigné'
    ]);
}
    #[Route('/', name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $reservationRepository->createQueryBuilder('r')
            ->leftJoin('r.trip', 't')
            ->addSelect('t');
    
        // Gestion de la recherche
        $searchTerm = $request->query->get('search');
        if ($searchTerm) {
            $queryBuilder
                ->andWhere('r.etatReservation LIKE :searchTerm OR t.departure_point LIKE :searchTerm OR t.destination LIKE :searchTerm')
                ->setParameter('searchTerm', '%'.$searchTerm.'%');
        }
    
        // Gestion du tri
        $sort = $request->query->get('sort', 'r.dateReservation');
        $direction = $request->query->get('direction', 'DESC');
        
        // Validation des champs de tri pour éviter les injections SQL
        $validSorts = ['r.dateReservation', 'r.etatReservation', 'r.montantTotal', 't.departure_point', 't.destination'];
        $sort = in_array($sort, $validSorts) ? $sort : 'r.dateReservation';
        $direction = in_array(strtoupper($direction), ['ASC', 'DESC']) ? strtoupper($direction) : 'DESC';
        
        $queryBuilder->orderBy($sort, $direction);
    
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
    
        return $this->render('reservation/index.html.twig', [
            'pagination' => $pagination,
            'sort' => $sort,
            'direction' => $direction,
            'searchTerm' => $searchTerm,
        ]);
    }
    #[Route('/admin', name: 'app_reservation_admin', methods: ['GET'])]
    public function indexadmin(ReservationRepository $reservationRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $reservationRepository->createQueryBuilder('r')
            ->leftJoin('r.trip', 't')
            ->addSelect('t');
    
        // Gestion de la recherche
        $searchTerm = $request->query->get('search');
        if ($searchTerm) {
            $queryBuilder
                ->andWhere('r.etatReservation LIKE :searchTerm OR t.departure_point LIKE :searchTerm OR t.destination LIKE :searchTerm')
                ->setParameter('searchTerm', '%'.$searchTerm.'%');
        }
    
        // Gestion du tri
        $sort = $request->query->get('sort', 'r.dateReservation');
        $direction = $request->query->get('direction', 'DESC');
        
        // Validation des champs de tri pour éviter les injections SQL
        $validSorts = ['r.dateReservation', 'r.etatReservation', 'r.montantTotal', 't.departure_point', 't.destination'];
        $sort = in_array($sort, $validSorts) ? $sort : 'r.dateReservation';
        $direction = in_array(strtoupper($direction), ['ASC', 'DESC']) ? strtoupper($direction) : 'DESC';
        
        $queryBuilder->orderBy($sort, $direction);
    
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
    
        return $this->render('reservation/admin_reservation.html.twig', [
            'pagination' => $pagination,
            'sort' => $sort,
            'direction' => $direction,
            'searchTerm' => $searchTerm,
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
    public function indexdriver(ReservationRepository $reservationRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $reservationRepository->createQueryBuilder('r')
            ->leftJoin('r.trip', 't')
            ->addSelect('t');
    
        // Gestion de la recherche
        $searchTerm = $request->query->get('search');
        if ($searchTerm) {
            $queryBuilder
                ->andWhere('r.etatReservation LIKE :searchTerm OR t.departure_point LIKE :searchTerm OR t.destination LIKE :searchTerm')
                ->setParameter('searchTerm', '%'.$searchTerm.'%');
        }
    
        // Gestion du tri
        $sort = $request->query->get('sort', 'r.dateReservation');
        $direction = $request->query->get('direction', 'DESC');
        
        // Validation des champs de tri pour éviter les injections SQL
        $validSorts = ['r.dateReservation', 'r.etatReservation', 'r.montantTotal', 't.departure_point', 't.destination'];
        $sort = in_array($sort, $validSorts) ? $sort : 'r.dateReservation';
        $direction = in_array(strtoupper($direction), ['ASC', 'DESC']) ? strtoupper($direction) : 'DESC';
        
        $queryBuilder->orderBy($sort, $direction);
    
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
    
        return $this->render('reservation/index_driver.html.twig', [
            'pagination' => $pagination,
            'sort' => $sort,
            'direction' => $direction,
            'searchTerm' => $searchTerm,
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
    #[Route('/admin/{id}', name: 'app_reservation_delete_admin', methods: ['POST'])]
    public function deleteadmin(Request $request, Reservation $reservation): Response
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

        return $this->redirectToRoute('app_reservation_admin');
    }
    #[Route('/driver/{id}', name: 'app_reservation_delete_driver', methods: ['POST'])]
    public function deletedriver(Request $request, Reservation $reservation): Response
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

        return $this->redirectToRoute('app_reservation_index_driver');
    }
    #[Route('/{id}/manage-driver', name: 'app_reservation_manage_driver', methods: ['GET', 'POST'])]
    public function manageDriver(
        Request $request, 
        Reservation $reservation,
        TwilioSmsService $twilioSmsService,
        ParameterBagInterface $params,
        LoggerInterface $logger
    ): Response {
        $logger->info('Managing reservation', ['id' => $reservation->getId()]);
        
        if ($request->isMethod('POST')) {
            $action = $request->request->get('action');
            $trip = $reservation->getTrip();
            
            if ($action === 'confirm') {
                if ($reservation->getNombrePlaces() > $trip->getAvailableSeats()) {
                    $this->addFlash('error', 'Not enough available seats');
                    return $this->redirectToRoute('app_reservation_manage_driver', ['id' => $reservation->getId()]);
                }
                
                $reservation->setEtatReservation('confirmé');
                $trip->setAvailableSeats($trip->getAvailableSeats() - $reservation->getNombrePlaces());
                
                // Send confirmation SMS
                $user = $reservation->getUser();
                $phoneNumber = $user?->getPhoneNumber() ?? $params->get('test_phone_number');
                
                if ($phoneNumber) {
                    $message = sprintf(
                        "EasyGo - Votre réservation a été confirmée avec succès. Accédez à votre compte pour plus de détails. Merci pour votre confiance !"
                    );
    
                    try {
                        if ($twilioSmsService->sendSms($phoneNumber, $message)) {
                            $this->addFlash('success', 'SMS de confirmation envoyé');
                        } else {
                            $this->addFlash('warning', 'Réservation confirmée mais SMS non envoyé');
                        }
                    } catch (\Exception $e) {
                        $logger->error('SMS sending error', ['error' => $e->getMessage()]);
                        $this->addFlash('warning', 'Erreur lors de l\'envoi du SMS');
                    }
                } else {
                    $this->addFlash('warning', 'Aucun numéro de téléphone disponible');
                }
                
                $this->addFlash('success', 'Réservation confirmée');
            } elseif ($action === 'reject') {
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
    
