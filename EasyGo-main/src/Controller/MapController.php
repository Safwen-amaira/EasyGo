<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Repository\TripRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MapController extends AbstractController
{
    #[Route('/vols/map', name: 'app_vol_map')]
    public function showMap(TripRepository $volRepository): Response
    {
        $vols = $volRepository->findAll();
    
        // Préparer un tableau simple pour JS avec les villes de départ et de destination
        $volData = [];
        foreach ($vols as $vol) {
            $volData[] = [
                'depart' => $vol->getDeparturePoint(),
                'destination' => $vol->getDestination()
            ];
        }
    
        // Afficher les données dans la console pour vérifier
        dump($volData);  // Vérifie le format des données
    
        return $this->render('trip/maps.html.twig', [
            'vols' => $volData
        ]);
    }
}
