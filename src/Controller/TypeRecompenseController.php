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
use Knp\Component\Pager\PaginatorInterface;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Route('/type-recompense', name: 'type_recompense_')]
class TypeRecompenseController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        $searchTerm = $request->query->get('search');
        $sort = $request->query->get('sort', 'id');
        $direction = strtoupper($request->query->get('direction', 'ASC')) === 'DESC' ? 'DESC' : 'ASC';
        $statut = $request->query->get('statut');
        $categorie = $request->query->get('categorie');
    
        $qb = $em->createQueryBuilder()
            ->select('t')
            ->from(TypeRecompense::class, 't');
    
        if ($searchTerm) {
            $qb->andWhere('LOWER(t.nom) LIKE :search')
                ->setParameter('search', '%' . strtolower($searchTerm) . '%');
        }
    
        if ($statut !== null && $statut !== '') {
            $qb->andWhere('t.actif = :statut')
                ->setParameter('statut', $statut === 'actif');
        }
    
        if ($categorie) {
            $qb->andWhere('LOWER(t.categorie) LIKE :cat')
                ->setParameter('cat', '%' . strtolower($categorie) . '%');
        }
    
        $allowedSortFields = ['id', 'nom', 'categorie'];
        if (in_array($sort, $allowedSortFields)) {
            $qb->orderBy("t.$sort", $direction);
        }
    
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );
    
        // AJAX response
        if ($request->isXmlHttpRequest()) {
            $data = [];
            foreach ($pagination as $type) {
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
            'types' => $pagination,
            'search' => $searchTerm,
            'sort' => $sort,
            'direction' => $direction,
            'statut' => $statut,
            'categorie' => $categorie,
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

    
    #[Route('/api/stats', name: 'api_stats')]
    public function apiStats(EntityManagerInterface $em): JsonResponse
    {
        $types = $em->getRepository(TypeRecompense::class)->findAll();
    
        $labels = [];
        $data = [];
    
        foreach ($types as $type) {
            $labels[] = $type->getNom();
            $data[] = count($type->getRecompenseFidelites());
        }
    
        return $this->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }
    
    #[Route('/stats', name: 'type_recompense_api_stats')]
    public function stats(): Response
    {
        return $this->render('type_recompense/stats.html.twig');
    }
    

#[Route('/export', name: 'export')]
public function exportExcel(Request $request, EntityManagerInterface $em): Response
{
    $searchTerm = $request->query->get('search');
    $statut = $request->query->get('statut');
    $categorie = $request->query->get('categorie');
    $sort = $request->query->get('sort', 'id');
    $direction = strtoupper($request->query->get('direction', 'ASC')) === 'DESC' ? 'DESC' : 'ASC';

    $qb = $em->createQueryBuilder()
        ->select('t')
        ->from(TypeRecompense::class, 't');

    if ($searchTerm) {
        $qb->andWhere('LOWER(t.nom) LIKE :search')
            ->setParameter('search', '%' . strtolower($searchTerm) . '%');
    }

    if ($statut !== null && $statut !== '') {
        $qb->andWhere('t.actif = :statut')
            ->setParameter('statut', $statut === 'actif');
    }

    if ($categorie) {
        $qb->andWhere('LOWER(t.categorie) LIKE :cat')
            ->setParameter('cat', '%' . strtolower($categorie) . '%');
    }

    $allowedSortFields = ['id', 'nom', 'categorie'];
    if (in_array($sort, $allowedSortFields)) {
        $qb->orderBy("t.$sort", $direction);
    }

    $types = $qb->getQuery()->getResult();

    // Excel creation
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header row
    $sheet->fromArray(['ID', 'Nom', 'Catégorie', 'Statut'], NULL, 'A1');

    // Data rows
    $row = 2;
    foreach ($types as $type) {
        $sheet->fromArray([
            $type->getId(),
            $type->getNom(),
            $type->getCategorie(),
            $type->isActif() ? 'Actif' : 'Inactif'
        ], NULL, 'A' . $row);
        $row++;
    }

    $writer = new Xlsx($spreadsheet);

    // Stream the response
    $response = new StreamedResponse(function () use ($writer) {
        $writer->save('php://output');
    });

    $filename = 'types_recompenses_' . date('Y-m-d_H-i-s') . '.xlsx';

    $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
    $response->headers->set('Cache-Control', 'max-age=0');

    return $response;
}

}
