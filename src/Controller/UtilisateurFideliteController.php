<?php

namespace App\Controller;

use App\Entity\Utilisateurfidelite;
use App\Form\UtilisateurFideliteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/utilisateur')]
class UtilisateurFideliteController extends AbstractController
{
    #[Route('/', name: 'utilisateur_index')]
public function index(Request $request, EntityManagerInterface $em): Response
{
    $searchTerm = $request->query->get('search');
    $repository = $em->getRepository(UtilisateurFidelite::class);

    $utilisateurs = $searchTerm
        ? $repository->createQueryBuilder('u')
            ->where('u.nomUtilisateur LIKE :search')
            ->setParameter('search', '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult()
        : $repository->findAll();

    return $this->render('utilisateur/index.html.twig', [
        'utilisateurs' => $utilisateurs,
        'search' => $searchTerm,
    ]);
}


    #[Route('/new', name: 'utilisateur_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $utilisateur = new Utilisateurfidelite();
        $form = $this->createForm(UtilisateurFideliteType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($utilisateur);
            $em->flush();

            return $this->redirectToRoute('utilisateur_index');
        }

        return $this->render('utilisateur/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'utilisateur_show')]
    public function show(Utilisateurfidelite $utilisateur): Response
    {
        return $this->render('utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    #[Route('/{id}/edit', name: 'utilisateur_edit')]
    public function edit(Request $request, Utilisateurfidelite $utilisateur, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UtilisateurFideliteType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('utilisateur_index');
        }

        return $this->render('utilisateur/edit.html.twig', [
            'form' => $form->createView(),
            'utilisateur' => $utilisateur,
        ]);
    }

    #[Route('/{id}/delete', name: 'utilisateur_delete')]
    public function delete(Utilisateurfidelite $utilisateur, EntityManagerInterface $em): Response
    {
        $em->remove($utilisateur);
        $em->flush();

        return $this->redirectToRoute('utilisateur_index');
    }
}
