<?php

// src/Controller/AdminReclamationController.php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Reponse;
use App\Form\ReponseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;           // ← import
use Symfony\Component\Mime\Email;                       // ← import
use Symfony\Component\Routing\Annotation\Route;
    
//

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
    public function reply(
        Reclamation $reclamation,
        Request $request,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer                // ← injection
    ): Response {
        $reponse = new Reponse();
        $reponse->setReclamation($reclamation);
        $reponse->setDateCreation(new \DateTime());

        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reponse);
            $entityManager->flush();

            // --- Envoi de l'email ---
            $email = (new Email())
                ->from('ademsahbeni30@gmail.com')
                ->to($reclamation->getEmail())
                ->subject('Réponse à votre réclamation')
                ->text(sprintf(
                    "Bonjour,\n\n"
                    ."Vous avez soumis une réclamation le %s.\n\n"
                    ."Description : %s\n\n"
                    ."Notre réponse : %s\n\n"
                    ."Cordialement,\nL'équipe support",
                    $reclamation->getDateCreation()->format('d/m/Y H:i'),
                    $reclamation->getDescription(),
                    // Adaptez getMessage() au nom de votre getter dans Reponse
                    $reponse->getContenu()  
                ))
            ;

            $mailer->send($email);
            // ————————————————

            $this->addFlash('success', 'Réponse enregistrée et email envoyé avec succès.');
            return $this->redirectToRoute('admin_reclamation_index');
        }

        return $this->render('admin/reclamation/reply.html.twig', [
            'form'        => $form->createView(),
            'reclamation' => $reclamation,
        ]);
    }
}

