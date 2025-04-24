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
use Knp\Component\Pager\PaginatorInterface;



#[Route('/recompense')]
class RecompenseFideliteController extends AbstractController
{

#[Route('/', name: 'recompense_index')]
public function index(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
{
    $searchTerm = $request->query->get('search');
    $sort = $request->query->get('sort', 'id');
    $direction = strtoupper($request->query->get('direction', 'ASC')) === 'DESC' ? 'DESC' : 'ASC';
    $typeFilter = $request->query->get('type');
    $userFilter = $request->query->get('user');

    $qb = $em->createQueryBuilder()
        ->select('r')
        ->from(RecompenseFidelite::class, 'r')
        ->leftJoin('r.typeRecompense', 't')
        ->leftJoin('r.utilisateurfidelite', 'u');

    if ($searchTerm) {
        $qb->andWhere('r.description LIKE :search')
            ->setParameter('search', '%' . $searchTerm . '%');
    }

    if ($typeFilter) {
        $qb->andWhere('t.nom LIKE :type')
            ->setParameter('type', '%' . $typeFilter . '%');
    }

    if ($userFilter) {
        $qb->andWhere('u.nomUtilisateur LIKE :user')
            ->setParameter('user', '%' . $userFilter . '%');
    }

    $allowedSortFields = ['id', 'description', 'points_requis', 'date_expiration'];
    if (in_array($sort, $allowedSortFields)) {
        $qb->orderBy("r.$sort", $direction);
    }

    $pagination = $paginator->paginate(
        $qb->getQuery(),
        $request->query->getInt('page', 1),
        10
    );

    if ($request->isXmlHttpRequest()) {
        $data = [];
        foreach ($pagination as $r) {
            $data[] = [
                'id' => $r->getId(),
                'description' => $r->getDescription(),
                'points' => $r->getPointsRequis(),
                'expiration' => $r->getDateExpiration()?->format('Y-m-d'),
                'type' => $r->getTypeRecompense()?->getNom(),
                'user' => $r->getUtilisateurfidelite()?->getNomUtilisateur(),
            ];
        }
        return new JsonResponse($data);
    }

    return $this->render('recompense/index.html.twig', [
        'recompenses' => $pagination,
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
    
        $user = $em->getRepository(Utilisateurfidelite::class)->find(2);
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], 404);
        }
    
        // Prevent duplicate
        $existing = $em->getRepository(RecompenseFidelite::class)->findOneBy([
            'utilisateurfidelite' => $user,
            'description' => $rewardLabel
        ]);
    
        if ($existing) {
            return new JsonResponse(['error' => 'Récompense déjà obtenue.'], 409);
        }
    
        // Fetch the base reward
        $base = $em->getRepository(RecompenseFidelite::class)->findOneBy([
            'description' => $rewardLabel,
            'utilisateurfidelite' => null
        ]);
    
        if (!$base) {
            return new JsonResponse(['error' => 'Récompense de base introuvable.'], 404);
        }
    
        // Clone for user
        $earned = new RecompenseFidelite();
        $earned->setDescription($base->getDescription());
        $earned->setUtilisateurfidelite($user);
        $earned->setDateExpiration((new \DateTime())->modify('+30 days'));
        $earned->setPointsRequis($base->getPointsRequis());
        $earned->setTypeRecompense($base->getTypeRecompense());
    
        // Add to user's points
        $user->setPointsAccumules($user->getPointsAccumules() + $base->getPointsRequis());
    
        $em->persist($earned);
        $em->flush();
    
        return new JsonResponse(['success' => true, 'recompense' => $earned->getDescription()]);
    }
    
#[Route('/spin/rewards', name: 'spin_rewards', methods: ['GET'])]
public function getRewards(EntityManagerInterface $em): JsonResponse
{
    // Fetch unique base rewards only (i.e., not user-earned)
    $baseRewards = $em->createQuery(
        'SELECT DISTINCT r.description FROM App\Entity\RecompenseFidelite r WHERE r.utilisateurfidelite IS NULL'
    )->getResult();

    $labels = array_map(fn($r) => $r['description'], $baseRewards);

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