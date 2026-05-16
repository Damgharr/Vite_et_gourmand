<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/employee')]
#[IsGranted('ROLE_EMPLOYEE')]
class EmployeeDashboardController extends AbstractController
{
    #[Route('/', name: 'employee_dashboard')]
    public function index(): Response
    {
        return $this->render('backoffice/employee_dashboard.html.twig');
    }
}
