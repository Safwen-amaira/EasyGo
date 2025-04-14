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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/vehicule')]
final class VehiculeController extends AbstractController{
    #[Route(name: 'app_vehicule_index', methods: ['GET'])]
    public function index(VehiculeRepository $vehiculeRepository): Response
    {
        return $this->render('vehicule/index.html.twig', [
            'vehicules' => $vehiculeRepository->findAll(),
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
            // Gérer l'upload de l'image
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('vehicule_images_directory'),
                        $newFilename
                    );

                    // Enregistrer le nom du fichier dans l'entité
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
    // Sauvegarde de l'ancienne image avant modification
    $oldImage = $vehicule->getImage();
    $form = $this->createForm(VehiculeType::class, $vehicule);
$form->handleRequest($request);


    if ($form->isSubmitted() && $form->isValid()) {
        // Vérifiez si une nouvelle image a été téléchargée
        $imageFile = $form->get('image')->getData();
        
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

            // Déplacez l'image téléchargée dans le dossier de stockage
            try {
                $imageFile->move(
                    $this->getParameter('vehicule_images_directory'),  // Dossier public/uploads/vehicules
                    $newFilename
                );
            } catch (FileException $e) {
                // Gérer l'erreur de téléchargement
                $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                return $this->redirectToRoute('app_vehicule_edit', ['id' => $vehicule->getId()]);
            }

            // Mettez à jour l'attribut "image" avec le nouveau nom du fichier
            $vehicule->setImage($newFilename);

            // Si une ancienne image existait, supprimez-la
            if ($oldImage) {
                $oldImagePath = $this->getParameter('vehicule_images_directory') . '/' . $oldImage;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);  // Supprime l'ancienne image
                }
            }
        }

        // Sauvegarde des modifications dans la base de données
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
        if ($this->isCsrfTokenValid('delete'.$vehicule->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($vehicule);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
    }
}



