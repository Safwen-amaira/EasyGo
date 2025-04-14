<?php

namespace App\Controller;
use App\Repository\RecompenseFideliteRepository;
use App\Repository\UtilisateurfideliteRepository;

use App\Entity\RecompenseFidelite;
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
        $repository = $em->getRepository(RecompenseFidelite::class);

        $recompenses = $searchTerm
            ? $repository->createQueryBuilder('r')
                ->where('r.description LIKE :search')
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
        $recompense = new RecompenseFidelite();
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
        $recompense = $em->getRepository(RecompenseFidelite::class)->find($id);

        if (!$recompense) {
            throw $this->createNotFoundException("Récompense introuvable.");
        }

        return $this->render('recompense/show.html.twig', [
            'recompense' => $recompense,
        ]);
    }

    #[Route('/{id}/edit', name: 'recompense_edit')]
    public function edit(Request $request, RecompenseFidelite $recompense, EntityManagerInterface $em): Response
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
    public function delete(RecompenseFidelite $recompense, EntityManagerInterface $em): Response
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

        $user = $em->getRepository(UtilisateurFidelite::class)->find($userId);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $rewards = $em->getRepository(RecompenseFidelite::class)->findBy([
            'utilisateurfidelite' => null
        ]);

        if (empty($rewards)) {
            return new JsonResponse(['error' => 'No unassigned rewards available'], 404);
        }

        $reward = $rewards[array_rand($rewards)];
        $reward->setUtilisateurFidelite($user);
        $reward->setStatut('attribuee');
        $em->flush();

        return new JsonResponse([
            'message' => 'Reward assigned!',
            'reward' => $reward->getDescription(),
        ]);
    }

    #[Route('/assign', name: 'assign_reward', methods: ['POST'])]
    public function assign(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        dump($data); // ← ajoute ça

        $rewardDescription = $data['reward'] ?? null;
        $userId = $data['userId'] ?? null;

        if (!$rewardDescription || !$userId) {
            return new JsonResponse(['error' => 'Missing data'], 400);
        }

        $user = $em->getRepository(UtilisateurFidelite::class)->find($userId);
        $reward = $em->getRepository(RecompenseFidelite::class)->findOneBy([
            'description' => $rewardDescription,
            'utilisateurfidelite' => null,
        ]);

        if (!$user || !$reward) {
            return new JsonResponse(['error' => 'User or reward not found or already assigned'], 404);
        }

        $reward->setUtilisateurFidelite($user);
        $reward->setStatut('attribuee');
        $em->flush();

        return new JsonResponse(['message' => 'Reward assigned!']);
    }

    #[Route('/list-json', name: 'reward_list_json')]
    public function listRewards(EntityManagerInterface $em): JsonResponse
    {
        $rewards = $em->getRepository(RecompenseFidelite::class)->findAll();

        $rewardDescriptions = array_map(fn($r) => $r->getDescription(), $rewards);

        return new JsonResponse(['rewards' => $rewardDescriptions]);
    }

    #[Route('/recompense/spin-page', name: 'recompense_spin_page')]
    public function spinPage(EntityManagerInterface $em): Response
    {$rewards = $em->getRepository(RecompenseFidelite::class)->findBy([
        'utilisateurfidelite' => null
    ]);
    
        

        $rewardDescriptions = array_map(fn($r) => $r->getDescription(), $rewards);

        return $this->render('recompense/spin.html.twig', [
            'rewardsJson' => json_encode($rewardDescriptions),
        ]);
    }


    #[Route('/mon-profil', name: 'mon_profil')]
    public function monProfil(UtilisateurfideliteRepository $utilisateurFideliteRepository): Response
    {
        // 1. Récupérer l'utilisateur spécifique (par exemple ID = 2)
        $user = $utilisateurFideliteRepository->find(2);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }
    
        // 2. Récupérer les récompenses de fidélité associées à cet utilisateur
        $recompenses = $user->getRecompenseFidelites();
    
        // 3. Passer les données à la vue Twig et retourner la réponse
        return $this->render('recompense/profil.html.twig', [
            'user'  => $user,
            'recompenses'  => $recompenses,
        ]);
    }
    



    #[Route('/statistiques', name: 'recompense_statistiques')]
    public function statistiques(EntityManagerInterface $em): Response
    {
        $total = $em->getRepository(RecompenseFidelite::class)->count([]);
    
        $avgPoints = $em->createQuery(
            'SELECT AVG(r.points_requis) FROM App\Entity\RecompenseFidelite r'
        )->getSingleScalarResult();
    
        $topTypes = $em->createQuery(
            'SELECT t.nom, COUNT(r.id) as total
             FROM App\Entity\RecompenseFidelite r
             JOIN r.typeRecompense t
             GROUP BY t.nom
             ORDER BY total DESC'
        )->setMaxResults(3)->getResult();
    
        $topUsers = $em->createQuery(
            'SELECT u FROM App\Entity\Utilisateurfidelite u ORDER BY u.points_accumules DESC'
        )->setMaxResults(5)->getResult();
    
        return $this->render('recompense/statistiques.html.twig', [
            'total' => $total,
            'avgPoints' => round($avgPoints),
            'topTypes' => $topTypes,
            'topUsers' => $topUsers,
        ]);
    }
    

}
