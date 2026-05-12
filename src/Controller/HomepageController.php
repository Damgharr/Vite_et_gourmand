<?php

namespace App\Controller;

use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Symfony\Component\Routing\Attribute\Route;

class HomepageController extends AbstractController
{
    #[Route("/", name: "homepage")]
    public function index(ReviewRepository $reviewRepository) : HttpFoundationResponse
    {
        $reviews = $reviewRepository->findLatest(10);

        return $this->render("pages/homepage.html.twig", [
            'reviews' => $reviews,
        ]);
    }

}