<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\ProfileCompletionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ProfileCompletionController extends AbstractController
{
    #[Route('/complete-profile', name: 'complete_profile')]
    public function completeProfile(
        #[CurrentUser] Users $user,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        // First check if profile is complete and redirect if true
        if ($user->isProfileComplete()) {
            if ($user->isAdmin()) {
                return $this->redirectToRoute('admin_dashboard');
            }
            if ($user->isRider()) {
                return $this->redirectToRoute('rider_dashboard');
            }
            if ($user->isDriver()) {
                return $this->redirectToRoute('driver_dashboard');
            }
            return $this->redirectToRoute('app_auth');
        }

        // Only create and handle form if profile is incomplete
        $form = $this->createForm(ProfileCompletionType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            if ($user->isAdmin()) {
                return $this->redirectToRoute('admin_dashboard');
            }
            if ($user->isRider()) {
                return $this->redirectToRoute('rider_dashboard');
            }
            if ($user->isDriver()) {
                return $this->redirectToRoute('driver_dashboard');
            }
            return $this->redirectToRoute('app_auth');
        }

        return $this->render('profile_completion/completion.html.twig', [
            'form' => $form->createView(),
            'errors' => $form->getErrors(true) // Pass form errors to template

        ]);
    }
}