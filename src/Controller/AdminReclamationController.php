<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationResponseType; // Formulaire pour la réponse
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminReclamationController extends AbstractController
{
    #[Route('/admin/reclamations', name: 'admin_reclamation_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupérer toutes les réclamations de la base de données
        $reclamations = $entityManager->getRepository(Reclamation::class)->findAll();

        return $this->render('admin/reclamation/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }
    #[Route('/admin/reclamation/{id}/change-status/{statut}', name: 'admin_reclamation_change_status')]
    public function changeStatus(Reclamation $reclamation, string $statut, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Si l'admin clique sur "Accepter" ou "Refuser", on met à jour le statut de la réclamation
        if (in_array($statut, ['Acceptée', 'Refusée'])) {
            $reclamation->setStatut($statut);
            $entityManager->persist($reclamation);
            $entityManager->flush();
            // Flash message pour informer l'admin que le statut a été mis à jour
            $this->addFlash('success', 'Statut de la réclamation mis à jour avec succès');
            return $this->redirectToRoute('admin_reclamation_index');
        }

        // Créer un formulaire pour répondre à la réclamation
       

        return $this->render('admin/reclamation/change_status.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }
    #[Route('/admin/reclamation/{id}/repondre', name: 'admin_reclamation_reply')]
public function reply(Reclamation $reclamation, Request $request, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(ReclamationResponseType::class, $reclamation);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($reclamation);
        $entityManager->flush();

        $this->addFlash('success', 'Réponse soumise avec succès');
        return $this->redirectToRoute('admin_reclamation_index');
    }

    return $this->render('admin/reclamation/change_status.html.twig', [
        'reclamation' => $reclamation,
        'form' => $form->createView(),
    ]);
}

}