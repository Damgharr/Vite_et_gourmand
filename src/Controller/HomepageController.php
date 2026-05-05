<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Symfony\Component\Routing\Attribute\Route;

class HomepageController extends AbstractController
{
    #[Route("/", name: "homepage")]
    public function index() : HttpFoundationResponse
    {
        return $this->render("homepage.html.twig");
    }

}