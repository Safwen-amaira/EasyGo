<?php

namespace App\Controller;

use App\Entity\Reservation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QrCodeController extends AbstractController
{
    #[Route('/reservation/qrcode/{id}', name: 'app_reservation_qrcode')]
    public function generateQrCode(Reservation $reservation): Response
    {
        $trip = $reservation->getTrip();
    
        if (!$trip) {
            throw $this->createNotFoundException('Trajet non trouvé');
        }
    
        // Créez une chaîne lisible plutôt qu'un tableau
        $qrContent = sprintf(
            "ID Réservation: %d\nTrajet: %s → %s\nDate: %s\nPlaces: %d\nMontant: %.2f DNT",
            $reservation->getId(),
            $trip->getDeparturePoint(),
            $trip->getDestination(),
            $reservation->getDateReservation()->format('d/m/Y'),
            $reservation->getNombrePlaces(),
            $reservation->getMontantTotal()
        );
    
        return $this->render('reservation/qrcode.html.twig', [
            'reservation' => $reservation,
            'trip' => $trip,
            'qrContent' => $qrContent // Envoyez directement la chaîne
        ]);
    }
}