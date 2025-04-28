<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FrontOfficeController extends AbstractController
{
    #[Route('/index', name: 'Home')]
    public function index(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'FrontOfficeController',
        ]);
    }
}
