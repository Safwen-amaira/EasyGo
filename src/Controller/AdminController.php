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
            return $this->redirectToRoute('admin_users');
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
        // Get user ID from localStorage through JavaScript
        return $this->render('admin/profile.html.twig');
    }
 


    #[Route('/api/profile', name: 'api_profile', methods: ['GET', 'POST'])]
    public function handleProfile(Request $request, EntityManagerInterface $em): JsonResponse
    {
        try {
            if ($request->isMethod('GET')) {
                $userId = $request->query->get('userId');
                return $this->handleGetProfile($userId, $em);
            }

            if ($request->isMethod('POST')) {
                return $this->handlePostProfile($request, $em);
            }

            return new JsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);

        } catch (\Exception $e) {
            return new JsonResponse(
                ['success' => false, 'error' => $e->getMessage()],
                500,
                ['Content-Type' => 'application/json']
            );
        }
    }

    private function handleGetProfile(?string $userId, EntityManagerInterface $em): JsonResponse
    {
        if (!$userId) {
            return new JsonResponse(['error' => 'User ID required'], 400);
        }

        $user = $em->getRepository(Users::class)->find($userId);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $profile = $em->getRepository(Profiles::class)->findOneBy(['userId' => $user]);

        return new JsonResponse([
            'exists' => $profile !== null,
            'nom' => $profile?->getNom(),
            'prenom' => $profile?->getPrenom(),
            'phone' => $profile?->getPhone(),
            'bio' => $profile?->getBio(),
            'requirements' => $profile?->getRequirements()
        ]);
    }

    private function handlePostProfile(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['error' => 'Invalid JSON format'], 400);
        }

        if (empty($data['userId'])) {
            return new JsonResponse(['error' => 'User ID required'], 400);
        }

        $user = $em->getRepository(Users::class)->find($data['userId']);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $requiredFields = ['nom', 'prenom', 'phone'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return new JsonResponse(['error' => "Missing required field: $field"], 400);
            }
        }

        if (!is_numeric($data['phone'])) {
            return new JsonResponse(['error' => 'Phone must be numeric'], 400);
        }

        $profile = $em->getRepository(Profiles::class)->findOneBy(['userId' => $user]);
        if (!$profile) {
            $profile = new Profiles();
            $profile->setUserId($user);
        }

        $profile->setNom($data['nom'])
            ->setPrenom($data['prenom'])
            ->setPhone((int)$data['phone'])
            ->setBio($data['bio'] ?? '')
            ->setRequirements($data['requirements'] ?? null);

        $em->persist($profile);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }


}