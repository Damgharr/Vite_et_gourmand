<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MenusController extends AbstractController
{
    #[Route('/menus', name: 'menus')]
    public function index(): Response
    {
        return $this->render("menus.html.twig");
    }
}
