<?php

namespace App\Controller;

use App\Entity\Feedback;
use App\Form\FeedbackType;
use App\Repository\FeedbackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Snipe\BanBuilder\CensorWords;

#[Route('/feedback')]
class FeedbackController extends AbstractController
{
    #[Route('/', name: 'app_feedback_index', methods: ['GET'])]
    public function index(FeedbackRepository $feedbackRepository): Response
    {
        return $this->render('feedback/index.html.twig', [
            'feedbacks' => $feedbackRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_feedback_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $feedback = new Feedback();
        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);
    
        $censor = new CensorWords;
        $langs = array('fr', 'it', 'en-us', 'en-uk', 'es');
        $badwords = $censor->setDictionary($langs);
        $censor->setReplaceChar("*");
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Nettoyage commentaire
            $string = $censor->censorString($feedback->getCommentaire());
            $feedback->setCommentaire($string['clean']);
    
            $entityManager->persist($feedback);
            $entityManager->flush();
    
            // 🔁 Réponse automatique selon la note
            $note = $feedback->getNote();
            if ($note >= 4) {
                $this->addFlash('success', '🙏 Merci pour votre retour positif ! Cela nous motive énormément.');
            } elseif ($note == 3) {
                $this->addFlash('info', 'Merci pour votre retour. Nous ferons de notre mieux pour nous améliorer.');
            } else {
                $this->addFlash('warning', '😔 Nous sommes désolés que votre expérience n’ait pas été satisfaisante. Nous allons analyser votre feedback pour nous améliorer.');
            }
    
            return $this->redirectToRoute('app_feedback_index');
        }
    
        return $this->render('feedback/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    

    #[Route('/edit/{id}', name: 'app_feedback_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Feedback $feedback, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_feedback_index');
        }

        return $this->render('feedback/edit.html.twig', [
            'form' => $form->createView(),
            'feedback' => $feedback,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_feedback_delete', methods: ['POST'])]
    public function delete(Request $request, Feedback $feedback, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$feedback->getId(), $request->request->get('_token'))) {
            $entityManager->remove($feedback);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_feedback_index');
    }
}
