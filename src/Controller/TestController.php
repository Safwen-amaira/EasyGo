<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TestController extends AbstractController{
    #[Route('/', name: 'app_test')]
    public function index(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
<<<<<<< HEAD

    #[Route('/frontt', name: 'app_testt')]
    public function indexx(): Response
    {
        return $this->render('fronttest.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
=======
>>>>>>> 2e8a2d4a8ada53e0c16bc283700a8efcda9ba6b2
}
