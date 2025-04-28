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
    private EntityManagerInterface $em;
    private JWTTokenManagerInterface $jwtManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        EntityManagerInterface $em,
        JWTTokenManagerInterface $jwtManager,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->em = $em;
        $this->jwtManager = $jwtManager;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function apiLogin(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['cin'], $data['password'])) {
            return $this->json(['error' => 'CIN and Password are required'], 400);
        }

        $user = $this->em->getRepository(Users::class)->findOneBy(['cin' => $data['cin']]);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $data['password'])) {
            return $this->json(['error' => 'Invalid credentials'], 401);
        }

        $token = $this->jwtManager->create($user);

        return $this->json([
            'token' => $token,
            'user' => [
                'id' => $user->getId(),
                'cin' => $user->getCin(),
                'email' => $user->getEmail(),
                'isAdmin' => $user->isAdmin(),
                'isDriver' => $user->isDriver(),
                'isRider' => $user->isRider(),
                'is2FAEnabled' => $user->is2FAEnabled(),
            ],
        ]);
    }
}
