<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function adminDashboard(): Response
    {
        return $this->render('dashboard/admin_dashboard.html.twig');
    }

    #[Route('/driver/dashboard', name: 'driver_dashboard')]
    public function driverDashboard(): Response
    {
        return $this->render('dashboard/driver_dashboard.html.twig');
    }

    #[Route('/rider/dashboard', name: 'rider_dashboard')]
    public function riderDashboard(): Response
    {
        return $this->render('dashboard/rider_dashboard.html.twig');
    }
}