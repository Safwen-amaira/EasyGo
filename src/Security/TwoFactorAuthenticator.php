<?php
namespace App\Security;

use App\Entity\Users;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class TwoFactorAuthenticator extends AbstractAuthenticator
{
    public function __construct(private RouterInterface $router)
    {
    }

    public function supports(Request $request): ?bool
    {
        return $request->isMethod('POST') && $this->router->generate('2fa_check') === $request->getPathInfo();
    }

    public function authenticate(Request $request): Passport
    {
        $code = $request->request->get('code');
        $user = $this->getUser();

        if (!$user instanceof Users || !$user->is2FAEnabled()) {
            throw new AuthenticationException('2FA not enabled');
        }

        if (!$this->isCodeValid($user, $code)) {
            throw new AuthenticationException('Invalid 2FA code');
        }

        return new SelfValidatingPassport(new UserBadge($user->getUserIdentifier()));
    }

    private function isCodeValid(Users $user, string $code): bool
    {
        $totp = \OTPHP\TOTP::create($user->getTotpSecret());
        return $totp->verify($code);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?RedirectResponse
    {
        return new RedirectResponse($this->router->generate('dashboard'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?RedirectResponse
    {
        return new RedirectResponse($this->router->generate('2fa_enter', ['error' => $exception->getMessage()]));
    }
}
