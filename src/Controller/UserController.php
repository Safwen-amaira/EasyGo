<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/', name: 'app_user')]


    public function index(): Response
    {

        return $this->render('base.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
