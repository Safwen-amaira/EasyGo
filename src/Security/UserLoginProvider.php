<?php

namespace App\Security;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserLoginProvider implements UserProviderInterface
{
    public function __construct(
        private readonly UsersRepository $userRepository
    ) {
    }
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->userRepository->findOneBy(['email' => $identifier]) ?? 
                $this->userRepository->findOneBy(['cin' => $identifier]);
    
        if (!$user) {
            throw new UserNotFoundException('Invalid credentials');
        }
    
        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return Users::class === $class || is_subclass_of($class, Users::class);
    }
}