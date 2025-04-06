<?php

namespace App\Controller;

use App\Entity\Recompensefidelite;
use App\Form\RecompenseFideliteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $form = $this->createForm(recompenseFideliteType::class, $recompense);
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

    #[Route('/{id}', name: 'recompense_show')]
    public function show(Recompensefidelite $recompense): Response
    {
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
}
