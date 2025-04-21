<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Reponse;
use App\Form\ReponseType;
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
        $reclamations = $entityManager->getRepository(Reclamation::class)->findAll();

        return $this->render('admin/reclamation/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    #[Route('/admin/reclamation/{id}/change-status/{statut}', name: 'admin_reclamation_change_status')]
    public function changeStatus(Reclamation $reclamation, string $statut, EntityManagerInterface $entityManager): Response
    {
        if (in_array($statut, ['Acceptée', 'Refusée'])) {
            $reclamation->setStatut($statut);
            $entityManager->flush();

            $this->addFlash('success', "Le statut a été mis à jour avec succès !");
        }

        return $this->redirectToRoute('admin_reclamation_index');
    }

    #[Route('/admin/reclamation/{id}/repondre', name: 'admin_reclamation_reply')]
    public function reply(Reclamation $reclamation, Request $request, EntityManagerInterface $entityManager): Response
    {
        $reponse = new Reponse();
        $reponse->setReclamation($reclamation);
        $reponse->setDateCreation(new \DateTime());
    
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reponse);
            $entityManager->flush();

            $this->addFlash('success', 'Réponse enregistrée avec succès.');
            return $this->redirectToRoute('admin_reclamation_index');
        }

        return $this->render('admin/reclamation/reply.html.twig', [
            'form' => $form->createView(),
            'reclamation' => $reclamation,
        ]);
    }
    
}
