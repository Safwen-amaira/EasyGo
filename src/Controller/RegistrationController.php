<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $plainPassword = $form->get('PasswordNet')->getData();

        $errorMessage = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            $user->setIsAdmin(false);
        
            $entityManager->persist($user);
            $entityManager->flush();
        
            $this->addFlash('success', 'Account created successfully! You can now log in.');
            return $this->redirectToRoute('app_auth'); // Redirect to login page
        }
        return $this->render('registration/registration.html.twig', [
            'registrationForm' => $form->createView(),
            'errorMessage' => $errorMessage,
            
        ]);
    }
    #[Route('/auth', name: 'app_auth')]
        public function authpage(): Response
        {
            return $this->render('auth/login.html.twig',[
                'registrationForm' => $form->createView(),
                'errorMessage' => $errorMessage,
            ]);
        }

}
