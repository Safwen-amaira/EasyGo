<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Profiles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse; 
use App\Form\ProfileType;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function dashboard(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository(Users::class)->findAll();
        
        return $this->render('admin/dashboard.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/admin/users', name: 'admin_users')]
    public function users(EntityManagerInterface $em): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $em->getRepository(Users::class)->findAll()
        ]);
    }

    #[Route('/admin/user/edit/{id}', name: 'admin_user_edit', methods: ['GET', 'POST'])]
    public function editUser(Request $request, Users $user, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $user->setNom($request->request->get('nom'));
            $user->setPrenom($request->request->get('prenom'));
            $user->setEmail($request->request->get('email'));
            $user->setCin($request->request->get('cin'));
            $user->setIsAdmin((bool)$request->request->get('isAdmin'));
            $user->setIsDriver((bool)$request->request->get('isDriver'));
            $user->setIsRider((bool)$request->request->get('isRider'));

            $em->flush();
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/edit.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/admin/user/delete/{id}', name: 'admin_user_delete', methods: ['POST'])]
    public function deleteUser(Users $user, EntityManagerInterface $em): Response
    {
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('admin_users');
    }
    #[Route('/admin/profile', name: 'admin_profile')]
    public function profile(Request $request, EntityManagerInterface $em): Response
    {
        // Get the user ID (simulate localStorage or URL parameter)
        $userId = $request->query->get('userId');

        if (!$userId) {
            throw $this->createNotFoundException('User ID missing.');
        }

        $user = $em->getRepository(Users::class)->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }

        // Find profile based on the relation
        $profile = $em->getRepository(Profiles::class)->findOneBy(['userId' => $user]);

        if (!$profile) {
            $profile = new Profiles();
            $profile->setUserId($user); // ✅ Correct setter method based on your entity
        }

        // Create the form
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($profile);
            $em->flush();

            $this->addFlash('success', 'Profile saved successfully.');
            return $this->redirectToRoute('admin_profile', ['userId' => $userId]);
        }

        return $this->render('admin/profile.html.twig', [
            'form' => $form->createView(),
            'userId' => $userId
        ]);
    }
}
