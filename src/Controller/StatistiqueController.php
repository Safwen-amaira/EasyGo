<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatistiqueController extends AbstractController
{
    #[Route('/statistiques', name: 'app_vehicule_stat')]
    public function index(EntityManagerInterface $em): Response
    {
        // Récupérer la liste des véhicules
        $vehicules = $em->getRepository(Vehicule::class)->findAll();

        // Statistiques
        $totalVehicules = count($vehicules);  // Nombre total de véhicules
        $totalPrix = array_sum(array_map(function ($vehicule) {
            return $vehicule->getPrix();  // Calcul du prix total de tous les véhicules
        }, $vehicules));
        $prixMoyen = $totalPrix / max($totalVehicules, 1);  // Prix moyen des véhicules
        
        // Compter les véhicules par catégorie
        $categories = $em->getRepository(Categorie::class)->findAll();
        $vehiculesParCategorie = [];
        foreach ($categories as $categorie) {
            $vehiculesParCategorie[$categorie->getName()] = count($em->getRepository(Vehicule::class)->findBy(['categorie' => $categorie]));
        }

        // Calculer d'autres statistiques si nécessaire (par exemple, les véhicules par couleur)
        $vehiculesParCouleur = [];
        foreach ($vehicules as $vehicule) {
            $couleur = $vehicule->getColor();
            if (!isset($vehiculesParCouleur[$couleur])) {
                $vehiculesParCouleur[$couleur] = 0;
            }
            $vehiculesParCouleur[$couleur]++;
        }

        // Passer les statistiques à la vue
        return $this->render('vehicule/statistiques.html.twig', [
            'totalVehicules' => $totalVehicules,
            'prixMoyen' => $prixMoyen,
            'vehiculesParCategorie' => $vehiculesParCategorie,
            'vehiculesParCouleur' => $vehiculesParCouleur,
        ]);
    }
}
