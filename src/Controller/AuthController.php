<?php

namespace App\Controller;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    #[Route('/api/login', name: 'api_login_check', methods: ['POST'])]
    public function ApiLogin(
        Request $request,
        EntityManagerInterface $em,
        JWTTokenManagerInterface $jwtManager,
        UserPasswordHasherInterface $hasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
    
        // Add validation
        if (!isset($data['cin']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'CIN and password are required'], 400);
        }
    
        $user = $em->getRepository(Users::class)->findOneBy(['cin' => $data['cin']]);
    
        if (!$user || !$hasher->isPasswordValid($user, $data['password'])) {
            return new JsonResponse(['error' => 'Invalid credentials'], 401);
        }
    
        // Explicitly return user data
        return new JsonResponse([
            'token' => $jwtManager->create($user),
            'user' => [
                'id' => $user->getId(),
                'cin' => $user->getCin(),
                'email' => $user->getEmail(),
                'isAdmin' => $user->isAdmin(),
                'isDriver' => $user->isDriver(),
                'isRider' => $user->isRider(),
                'is2_faenabled'=>$user->is2FAEnabled()
            ]
        ]);
    }
}