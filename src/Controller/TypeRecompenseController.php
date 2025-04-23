<?php

namespace App\Controller;
use App\Entity\RecompenseFidelite;

use App\Entity\TypeRecompense;
use App\Form\TypeRecompenseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\Component\HttpFoundation\JsonResponse;



#[Route('/type-recompense', name: 'type_recompense_')]
class TypeRecompenseController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $searchTerm = $request->query->get('search');
        $sort = $request->query->get('sort', 'id');
        $direction = strtoupper($request->query->get('direction', 'ASC')) === 'DESC' ? 'DESC' : 'ASC';
    
        $qb = $em->createQueryBuilder()
            ->select('t')
            ->from(TypeRecompense::class, 't');
    
        if ($searchTerm) {
            $qb->where('LOWER(t.nom) LIKE :search')
               ->setParameter('search', '%' . strtolower($searchTerm) . '%');
        }
    
        $allowedSortFields = ['id', 'nom', 'categorie'];
        if (in_array($sort, $allowedSortFields)) {
            $qb->orderBy("t.$sort", $direction);
        }
    
        $types = $qb->getQuery()->getResult();
    
        if ($request->isXmlHttpRequest()) {
            $data = [];
            foreach ($types as $type) {
                $data[] = [
                    'id' => $type->getId(),
                    'nom' => $type->getNom(),
                    'categorie' => $type->getCategorie(),
                    'actif' => $type->isActif(),
                ];
            }
            return new JsonResponse($data);
        }
    
        return $this->render('type_recompense/index.html.twig', [
            'types' => $types,
            'search' => $searchTerm,
        ]);
    }
    

    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $type = new TypeRecompense();
        $form = $this->createForm(TypeRecompenseType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($type);
            $em->flush();

            return $this->redirectToRoute('type_recompense_index');
        }

        return $this->render('type_recompense/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(Request $request, TypeRecompense $type, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TypeRecompenseType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('type_recompense_index');
        }

        return $this->render('type_recompense/edit.html.twig', [
            'form' => $form->createView(),
            'type' => $type,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete')]
    public function delete(TypeRecompense $type, EntityManagerInterface $em): Response
    {
        $em->remove($type);
        $em->flush();

        return $this->redirectToRoute('type_recompense_index');
    }

    #[Route('/toggle/{id}', name: 'toggle')]
    public function toggle(TypeRecompense $type, EntityManagerInterface $em): Response
    {
        $type->setActif(!$type->isActif());
        $em->flush();

        return $this->redirectToRoute('type_recompense_index');
 
    }

    
    
    #[Route('/stats', name: 'type_recompense_stats')]
public function stats(EntityManagerInterface $em, ChartBuilderInterface $chartBuilder): Response
{
    $types = $em->getRepository(TypeRecompense::class)->findAll();

    $labels = [];
    $data = [];

    foreach ($types as $type) {
        $labels[] = $type->getNom();
        $data[] = count($type->getRecompenseFidelites()); // attention: relation OneToMany
    }

    $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
    $chart->setData([
        'labels' => $labels,
        'datasets' => [[
            'label' => 'Nombre de récompenses',
            'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
            'borderColor' => 'rgba(54, 162, 235, 1)',
            'data' => $data,
        ]],
    ]);
    $chart->setOptions([
        'responsive' => true,
        'plugins' => ['legend' => ['position' => 'top']],
    ]);

    return $this->render('type_recompense/stats.html.twig', [
        'chart' => $chart,
    ]);
}

}
