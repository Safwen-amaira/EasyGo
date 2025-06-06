<?php

namespace App\Security;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class GoogleAuthenticator extends OAuth2Authenticator implements AuthenticationEntryPointInterface
{
    private $clientRegistry;
    private $entityManager;
    private $router;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function() use ($accessToken, $client) {
                /** @var GoogleUser $googleUser */
                $googleUser = $client->fetchUserFromToken($accessToken);
                
                $email = $googleUser->getEmail();
                $user = $this->entityManager->getRepository(Users::class)->findOneBy(['email' => $email]);

                if (!$user) {
                    // Create new user
                    $user = new Users();
                    $user->setEmail($email);
                    $user->setGoogleId($googleUser->getId());
                    $user->setNom($googleUser->getFirstName());
                    $user->setPrenom($googleUser->getLastName());   
                    $user->setIsRider(false);
                    $user->setIsDriver(false);
                    $user->setIsAdmin(false);

                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                }

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();
        
        // Check if profile is complete
        if (!$user->isProfileComplete()) {
            return new RedirectResponse($this->router->generate('complete_profile'));
        }

       
        // Redirect based on user role
       if($user->isAdmin()){

        $targetUrl = $this->router->generate('admin_dashboard');
       }
       if($user->isRider()){

        $targetUrl = $this->router->generate('rider_dashboard');
       } 
          if($user->isDriver()){

        $targetUrl = $this->router->generate('driver_dashboard');
       }
       $userData = [
        'id' => $user->getId(),
        'isAdmin' => $user->isAdmin(),
        'isDriver' => $user->isDriver(),
        'isRider' => $user->isRider(),
        'cin' => $user->getCin(),
        'email' => $user->getEmail(),
        'nom' => $user->getNom(),
        'prenom' => $user->getPrenom()
    ];
    $targetUrl = $this->router->generate($user->isAdmin() ? 'admin_dashboard' : 
    ($user->isRider() ? 'rider_dashboard' : 'driver_dashboard'));
    $content = sprintf(
        '<script>
            localStorage.setItem("user", JSON.stringify(%s));
            window.location.href = "%s";
        </script>',
        json_encode($userData),
        $targetUrl
    );
    return new Response($content);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());
        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse(
            $this->router->generate('connect_google'),
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}