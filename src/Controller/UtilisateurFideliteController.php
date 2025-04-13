<?php

namespace App\Controller;

use App\Entity\Utilisateurfidelite;
use App\Form\UtilisateurFideliteType;
use App\Repository\UtilisateurfideliteRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/utilisateur-fidelite')]
class UtilisateurFideliteController extends AbstractController
{
    #[Route('/', name: 'utilisateur_fidelite_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository(Utilisateurfidelite::class)->findAll();

        return $this->render('utilisateur_fidelite/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/{id}/edit', name: 'utilisateur_fidelite_edit')]
    public function edit(Request $request, Utilisateurfidelite $user, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UtilisateurFideliteType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('utilisateur_fidelite_index');
        }

        return $this->render('utilisateur_fidelite/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/{id}/delete', name: 'utilisateur_fidelite_delete')]
    public function delete(Utilisateurfidelite $user, EntityManagerInterface $em): Response
    {
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('utilisateur_fidelite_index');
    }

    #[Route('/add-points/{id}/{points}', name: 'utilisateur_fidelite_add_points')]
    public function addPoints(int $id, int $points, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(Utilisateurfidelite::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException("User not found.");
        }

        $user->setPointsAccumulés($user->getPointsAccumulés() + $points);
        $em->flush();

        return $this->redirectToRoute('utilisateur_fidelite_index');
    }
    


}
