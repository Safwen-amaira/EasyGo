<?php

namespace App\Controller;
use App\Repository\RecompenseFideliteRepository;
use App\Repository\UtilisateurfideliteRepository;

use App\Entity\RecompenseFidelite;
use App\Entity\TypeRecompense;

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
    
    // Handle the form submission
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

    

    #[Route('/spin', name: 'recompense_spin', methods: ['POST'])]
public function spinReward(Request $request, EntityManagerInterface $em): JsonResponse
{
    $data = json_decode($request->getContent(), true);
    $rewardLabel = $data['reward'] ?? null;

    if (!$rewardLabel) {
        return new JsonResponse(['error' => 'Récompense manquante'], 400);
    }

    // Constant user: ID = 2
    $user = $em->getRepository(Utilisateurfidelite::class)->find(2);
    if (!$user) {
        return new JsonResponse(['error' => 'Utilisateur non trouvé'], 404);
    }

    $reward = new RecompenseFidelite();
    $reward->setDescription($rewardLabel);
    $reward->setUtilisateurfidelite($user);
    $reward->setDateExpiration((new \DateTime())->modify('+30 days'));
    $reward->setPointsRequis(0); // You can randomize or fetch if needed

    $em->persist($reward);
    $em->flush();

    return new JsonResponse(['success' => true, 'recompense' => $rewardLabel]);
}

#[Route('/spin/rewards', name: 'spin_rewards', methods: ['GET'])]
public function getRewards(EntityManagerInterface $em): JsonResponse
{
    $rewards = $em->getRepository(RecompenseFidelite::class)->findAll();

    $labels = array_map(fn($reward) => $reward->getDescription(), $rewards);

    if (empty($labels)) {
        return new JsonResponse(['error' => 'Aucune récompense disponible'], 404);
    }

    return new JsonResponse($labels);
}


#[Route('/spin-page', name: 'recompense_spin_page')]
public function spinPage(): Response
{
    return $this->render('recompense/spin.html.twig');
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
        'now' => new \DateTime(), // Pass the current time to the template
    ]);
}




private function getRecompenseStats(EntityManagerInterface $em): array
{
    $total = $em->getRepository(RecompenseFidelite::class)->count([]);

    $avgPoints = $em->createQuery(
        'SELECT AVG(r.points_requis) FROM App\Entity\RecompenseFidelite r'
    )->getSingleScalarResult();

    return ['total' => $total, 'avgPoints' => round($avgPoints)];
}
private function getTopRewardTypes(EntityManagerInterface $em): array
{
    return $em->createQuery(
        'SELECT t.nom, COUNT(r.id) as total
         FROM App\Entity\RecompenseFidelite r
         JOIN r.typeRecompense t
         GROUP BY t.nom
         ORDER BY total DESC'
    )->setMaxResults(3)->getResult();
}
private function getTopUsersForChart(EntityManagerInterface $em): array
{
    $topUsers = $em->createQuery(
        'SELECT u FROM App\Entity\Utilisateurfidelite u ORDER BY u.points_accumules DESC'
    )->setMaxResults(5)->getResult();

    $labels = [];
    $points = [];

    foreach ($topUsers as $user) {
        $labels[] = $user->getNomUtilisateur();
        $points[] = $user->getPointsAccumules();
    }

    return [
        'topUsers' => $topUsers,
        'labels' => json_encode($labels),
        'points' => json_encode($points),
    ];
}


#[Route('/statistiques', name: 'recompense_statistiques')]
public function statistiques(EntityManagerInterface $em): Response
{
    $stats = $this->getRecompenseStats($em);
    $topTypes = $this->getTopRewardTypes($em);
    $userChartData = $this->getTopUsersForChart($em);

    return $this->render('recompense/statistiques.html.twig', [
        'total' => $stats['total'],
        'avgPoints' => $stats['avgPoints'],
        'topTypes' => $topTypes,
        'topUsers' => $userChartData['topUsers'],
        'labels' => $userChartData['labels'],
        'points' => $userChartData['points'],
    ]);
}
}