<?php

namespace App\Controller;

use App\Entity\Recompensefidelite;
use App\Entity\Utilisateurfidelite;
use App\Form\RecompenseFideliteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/recompense')]
class RecompenseFideliteController extends AbstractController
{
    #[Route('/', name: 'recompense_index')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $searchTerm = $request->query->get('search');
        $repository = $em->getRepository(Recompensefidelite::class);

        $recompenses = $searchTerm
            ? $repository->createQueryBuilder('r')
                ->where('r.descriptionRecompense LIKE :search')
                ->setParameter('search', '%' . $searchTerm . '%')
                ->getQuery()
                ->getResult()
            : $repository->findAll();

        return $this->render('recompense/index.html.twig', [
            'recompenses' => $recompenses,
            'search' => $searchTerm,
        ]);
    }

    #[Route('/new', name: 'recompense_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $recompense = new Recompensefidelite();
        $form = $this->createForm(RecompenseFideliteType::class, $recompense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($recompense);
            $em->flush();

            return $this->redirectToRoute('recompense_index');
        }

        return $this->render('recompense/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/show/{id}', name: 'recompense_show')]
public function show(int $id, EntityManagerInterface $em): Response
{
    $recompense = $em->getRepository(Recompensefidelite::class)->find($id);

    if (!$recompense) {
        throw $this->createNotFoundException("Récompense introuvable.");
    }

    return $this->render('recompense/show.html.twig', [
        'recompense' => $recompense,
    ]);
}


    #[Route('/{id}/edit', name: 'recompense_edit')]
    public function edit(Request $request, Recompensefidelite $recompense, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(RecompenseFideliteType::class, $recompense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('recompense_index');
        }

        return $this->render('recompense/edit.html.twig', [
            'form' => $form->createView(),
            'recompense' => $recompense,
        ]);
    }

    #[Route('/{id}/delete', name: 'recompense_delete')]
    public function delete(Recompensefidelite $recompense, EntityManagerInterface $em): Response
    {
        $em->remove($recompense);
        $em->flush();

        return $this->redirectToRoute('recompense_index');
    }

    #[Route('/spin', name: 'spin_reward', methods: ['POST'])]
    public function spin(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $userId = $data['userId'] ?? null;
    
        if (!$userId) {
            return new JsonResponse(['error' => 'Missing user ID'], 400);
        }
    
        $user = $em->getRepository(Utilisateurfidelite::class)->find($userId);
    
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }
    
        $rewards = $em->getRepository(Recompensefidelite::class)->findBy([
            'Utilisateurfidelite' => null
        ]);
    
        if (empty($rewards)) {
            return new JsonResponse(['error' => 'No unassigned rewards available'], 404);
        }
    
        $reward = $rewards[array_rand($rewards)];
        $reward->setUtilisateurfidelite($user);
        $em->flush();
    
        return new JsonResponse([
            'message' => 'Reward assigned!',
            'reward' => $reward->getDescriptionRecompense()
        ]);
    }
    
    #[Route('/assign', name: 'assign_reward', methods: ['POST'])]
    public function assign(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $rewardDescription = $data['reward'] ?? null;
        $userId = $data['userId'] ?? null;

        if (!$rewardDescription || !$userId) {
            return new JsonResponse(['error' => 'Missing data'], 400);
        }

        $user = $em->getRepository(Utilisateurfidelite::class)->find($userId);
        $reward = $em->getRepository(Recompensefidelite::class)->findOneBy([
            'descriptionRecompense' => $rewardDescription,
            'Utilisateurfidelite' => null // 👈 assure que la récompense n’est pas déjà assignée
        ]);

        if (!$user || !$reward) {
            return new JsonResponse(['error' => 'User or reward not found or already assigned'], 404);
        }

        $reward->setUtilisateurfidelite($user);
        $em->flush();

        return new JsonResponse(['message' => 'Reward assigned!']);
    }

    #[Route('/list-json', name: 'reward_list_json')]
    public function listRewards(EntityManagerInterface $em): JsonResponse
    {
        $rewards = $em->getRepository(Recompensefidelite::class)->findAll();

        $rewardDescriptions = array_map(function ($reward) {
            return $reward->getDescriptionRecompense();
        }, $rewards);

        return new JsonResponse(['rewards' => $rewardDescriptions]);
    }
    #[Route('/recompense/spin-page', name: 'recompense_spin_page')]
public function spinPage(EntityManagerInterface $em): Response
{
    $rewards = $em->getRepository(Recompensefidelite::class)->findAll();

    return $this->render('recompense/spin.html.twig', [
        'rewardsJson' => json_encode($rewards),
    ]);
}

}
