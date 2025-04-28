<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\UsersRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
#[ORM\Table(name: "easygo_users")]
#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 8)]
    #[Groups(['user:read'])]
    private ?string $cin = null;
    
    #[ORM\OneToOne(mappedBy: 'userId', cascade: ['persist', 'remove'])]
    #[Ignore]
    private ?Profiles $profile = null;
    
    #[ORM\Column]
    private ?bool $isRider = null;

    #[ORM\Column]
    private ?bool $isAdmin = null;

    #[ORM\Column]
    private ?bool $isDriver = null;

    
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $totpSecret = null;

    #[ORM\Column(type: 'boolean')]
    private bool $is2FAEnabled = false;

    // Getters and Setters
    public function getTotpSecret(): ?string
    {
        return $this->totpSecret;
    }

    public function setTotpSecret(?string $totpSecret): self
    {
        $this->totpSecret = $totpSecret;
        return $this;
    }

    public function is2FAEnabled(): bool
    {
        return $this->is2FAEnabled;
    }

    public function setIs2FAEnabled(bool $is2FAEnabled): self
    {
        $this->is2FAEnabled = $is2FAEnabled;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getCin(): ?string
    {
        return $this->cin;
    }

    public function setCin(string $cin): static
    {
        $this->cin = $cin;
        return $this;
    }

    public function getProfile(): ?Profiles
    {
        return $this->profile;
    }

    public function setProfile(Profiles $profile): static
    {
        // set the owning side of the relation if necessary
        if ($profile->getUserId() !== $this) {
            $profile->setUserId($this);
        }
        $this->profile = $profile;
        return $this;
    }

    public function isRider(): ?bool
    {
        return $this->isRider;
    }

    public function setIsRider(bool $isRider): static
    {
        $this->isRider = $isRider;
        return $this;
    }

    public function isAdmin(): ?bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): static
    {
        $this->isAdmin = $isAdmin;
        return $this;
    }

    public function isDriver(): ?bool
    {
        return $this->isDriver;
    }

    public function setIsDriver(bool $isDriver): static
    {
        $this->isDriver = $isDriver;
        return $this;
    }

    #[Groups(['user:read'])]
    public function getUserIdentifier(): string
    {
        return $this->cin;
    }

    #[Groups(['user:read'])]
    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];
        if ($this->isAdmin === true) {
            $roles[] = 'ROLE_ADMIN';
        }
        if ($this->isDriver === true) {
            $roles[] = 'ROLE_DRIVER';
        }
        if ($this->isRider === true) {
            $roles[] = 'ROLE_RIDER';
        }
        return array_unique($roles);
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary sensitive data on the user, clear it here
    }
}


class UserService
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function encodePassword(Users $user, string $plainPassword): void
    {
        $encodedPassword = $this->passwordEncoder->encodePassword($user, $plainPassword);
        $user->setPassword($encodedPassword);
    }
}

