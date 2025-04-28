<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Form\VehiculeType;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/vehicule')]
final class VehiculeController extends AbstractController
{
    #[Route(name: 'app_vehicule_index', methods: ['GET'])]
    public function index(VehiculeRepository $vehiculeRepository): Response
    {
        return $this->render('vehicule/index.html.twig', [
            'vehicules' => $vehiculeRepository->findAll(),
        ]);
    }

    #[Route('/uservehicule', name: 'app_vehicule_indexx', methods: ['GET'])]
    public function indexx(Request $request, VehiculeRepository $vehiculeRepository, PaginatorInterface $paginator): Response
    {
        $query = $vehiculeRepository->createQueryBuilder('v')->getQuery();
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('uservehicule/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/{id}/show', name: 'app_vehicule_showw', methods: ['GET'])]
    public function showw(Vehicule $vehicule): Response
    {
        return $this->render('uservehicule/show.html.twig', [
            'vehicule' => $vehicule,
        ]);
    }

    #[Route('/vehicule/searchh', name: 'app_vehicule_searcc', methods: ['GET'])]
    public function searchh(Request $request, VehiculeRepository $vehiculeRepository, PaginatorInterface $paginator): Response
    {
      $searchTerm = $request->query->get('search');
        
        $query = $vehiculeRepository->searchByName($searchTerm);

        $pagination = $paginator->paginate(
            $query, // le query builder ou tableau
            $request->query->getInt('page', 1), // page actuelle, par défaut 1
            6 // nombre d'éléments par page
        );

        return $this->render('vehicule/search.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/vehicule/search', name: 'app_vehicule_search', methods: ['GET'])]
    public function search(Request $request, VehiculeRepository $vehiculeRepository): Response
    {
        $search = $request->query->get('search');
        $vehicules = $vehiculeRepository->searchByName($search);

        return $this->render('vehicule/_vehicules_table.html.twig', [
            'vehicules' => $vehicules,
        ]);
    }

    #[Route('/new', name: 'app_vehicule_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vehicule = new Vehicule();
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('vehicule_images_directory'),
                        $newFilename
                    );
                    $vehicule->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                }
            }

            $entityManager->persist($vehicule);
            $entityManager->flush();

            return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vehicule/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_vehicule_show', methods: ['GET'])]
    public function show(Vehicule $vehicule): Response
    {
        return $this->render('vehicule/show.html.twig', [
            'vehicule' => $vehicule,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_vehicule_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vehicule $vehicule, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $oldImage = $vehicule->getImage();
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('vehicule_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                    return $this->redirectToRoute('app_vehicule_edit', ['id' => $vehicule->getId()]);
                }

                $vehicule->setImage($newFilename);

                if ($oldImage) {
                    $oldImagePath = $this->getParameter('vehicule_images_directory') . '/' . $oldImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vehicule/edit.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_vehicule_delete', methods: ['POST'])]
    public function delete(Request $request, Vehicule $vehicule, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $vehicule->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($vehicule);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/vehicule/calendar', name: 'app_vehicule_calendar')]
    public function calendrier(VehiculeRepository $vehiculeRepository): Response
    {
        $vehicules = $vehiculeRepository->searchByNameWithEtat(null);

        return $this->render('vehicule/calendar.html.twig', [
            'vehicules' => $vehicules,
        ]);
    }

    #[Route('/vehicule/events', name: 'app_vehicule_events')]
    public function events(VehiculeRepository $vehiculeRepository): JsonResponse
    {
        $vehicules = $vehiculeRepository->findAll();
        $today = new \DateTime();

        $events = array_map(function ($vehicule) use ($today) {
            $createdDate = $vehicule->getCreated();
            $etat = $createdDate > $today ? 'Disponible' : 'Attente';

            return [
                'id' => $vehicule->getId(),
                'title' => $vehicule->getName() . ' - ' . $etat,
                'start' => $createdDate->format('Y-m-d'),
                'backgroundColor' => $etat === 'Disponible' ? 'green' : 'red',
            ];
        }, $vehicules);

        return new JsonResponse($events);
    }
}
