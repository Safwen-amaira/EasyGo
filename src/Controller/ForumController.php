<?php

namespace App\Controller;

use App\Entity\Forum;
use App\Form\ForumType;
use App\Repository\ForumRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/forum')]
final class ForumController extends AbstractController
{
    #[Route(name: 'app_forum_index', methods: ['GET'])]
    public function index(ForumRepository $forumRepository): Response
    {
        return $this->render('forum/index.html.twig', [
            'forums' => $forumRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_forum_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $forum = new Forum();
        $form = $this->createForm(ForumType::class, $forum);
        $form->handleRequest($request);
        $forum->setDate(new \DateTime());

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($forum);
            $entityManager->flush();

            return $this->redirectToRoute('app_forum_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('forum/new.html.twig', [
            'forum' => $forum,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_forum_show', methods: ['GET'])]
    public function show(Forum $forum): Response
    {
        $feedbacks = $forum->getFeedback();
    
        $stats = [
            'total' => count($feedbacks),
            'average' => 0,
            'distribution' => [0, 0, 0, 0, 0], // index 0 => note 1, index 4 => note 5
            'most_common' => null,
            'positive' => 0,
            'neutral' => 0,
            'negative' => 0,
            'std_deviation' => 0,
        ];
    
        if ($stats['total'] > 0) {
            $sum = 0;
            $notes = [];
    
            foreach ($feedbacks as $feedback) {
                $note = $feedback->getNote();
                $sum += $note;
                $notes[] = $note;
                $stats['distribution'][$note - 1]++;
    
                // Catégorisation
                if ($note >= 4) {
                    $stats['positive']++;
                } elseif ($note == 3) {
                    $stats['neutral']++;
                } else {
                    $stats['negative']++;
                }
            }
    
            // Moyenne
            $stats['average'] = round($sum / $stats['total'], 2);
    
            // Répartition en %
            foreach ($stats['distribution'] as $i => $count) {
                $stats['distribution'][$i] = round(($count / $stats['total']) * 100, 1);
            }
    
            // Note la plus fréquente
            $note_counts = array_count_values($notes);
            arsort($note_counts);
            $stats['most_common'] = array_key_first($note_counts);
    
            // Écart-type
            $mean = $stats['average'];
            $variance = array_reduce($notes, fn($carry, $n) => $carry + pow($n - $mean, 2), 0) / $stats['total'];
            $stats['std_deviation'] = round(sqrt($variance), 2);
        }
    
        return $this->render('forum/show.html.twig', [
            'forum' => $forum,
            'stats' => $stats,
        ]);
    }
    
    #[Route('/{id}/edit', name: 'app_forum_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Forum $forum, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ForumType::class, $forum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_forum_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('forum/edit.html.twig', [
            'forum' => $forum,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_forum_delete', methods: ['POST'])]
    public function delete(Request $request, Forum $forum, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$forum->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($forum);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_forum_index', [], Response::HTTP_SEE_OTHER);
    }
}
