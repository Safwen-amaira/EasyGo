<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Users;
//for the 2FA : 
use Doctrine\ORM\EntityManagerInterface;
use OTPHP\TOTP;
use Symfony\Component\HttpFoundation\Request;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class SecurityController extends AbstractController
{
    #[Route('/auth', name: 'app_auth')]
    public function authpage(): Response
    {
        return $this->render('auth/login.html.twig');
    }

    #[Route('/dashboard',name:'app_dashboard')]
    public function dashboard():Response
{
    return $this ->render ('auth/dashboard.html.twig');
}

#[Route('/2fa/setup', name: '2fa_setup')]
public function setup2fa(EntityManagerInterface $em): Response
{
    /** @var Users $user */
    $user = $this->getUser();
    
    if (!$user) {
        return $this->redirectToRoute('app_auth');
    }
    if (!$user->is2FAEnabled()) {
        $totp = TOTP::create();
        $user->setTotpSecret($totp->getSecret());
        $em->flush();
    }

    $qrCode = $this->generateQrCode($user);
    
    return $this->render('security/2fa_setup.html.twig', [
        'qrCode' => $qrCode,
        'secret' => $user->getTotpSecret()
    ]);
}

#[Route('/2fa/enter', name: '2fa_enter')]
public function enter2fa(Request $request): Response
{
    return $this->render('security/2fa_enter.html.twig', [
        'error' => $request->query->get('error')
    ]);
}
#[Route('/2fa/check', name: '2fa_check', methods: ['POST'])]
public function check2fa(Request $request, EntityManagerInterface $em): Response
{
    // Get the email directly from the request
    $email = $request->request->get('user_identifier'); // Changed from 'user' to 'user_identifier'
    $user = $em->getRepository(Users::class)->findOneBy(['email' => $email]); // Removed .getEmail()

    if (!$user) {
        return $this->redirectToRoute('app_auth');
    }

    $code = $request->request->get('code');
    if ($this->isCodeValid($user, $code)) {
        $user->setIs2FAEnabled(true);
        $em->flush();
        return $this->redirectToRoute('admin_dashboard');
    }

    return $this->redirectToRoute('2fa_enter', ['error' => 'Invalid code']);
}

private function generateQrCode(Users $user): string
{
    $totp = TOTP::create($user->getTotpSecret());
    $totp->setLabel($user->getEmail());
    
    $renderer = new ImageRenderer(
        new RendererStyle(400),
        new SvgImageBackEnd()
    );
    
    $writer = new Writer($renderer);
    return 'data:image/svg+xml;base64,' . base64_encode($writer->writeString($totp->getProvisioningUri()));
}

private function isCodeValid(Users $user, string $code): bool
{
    $totp = TOTP::create($user->getTotpSecret());
    return $totp->verify($code);
}
}
