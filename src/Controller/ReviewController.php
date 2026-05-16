<?php

namespace App\Controller;

use App\Entity\Review;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/employee')]
#[IsGranted('ROLE_EMPLOYEE')]
class ReviewController extends AbstractController
{
    #[Route('/reviews', name: 'employee_reviews')]
    public function index(ReviewRepository $reviewRepository, Request $request): Response
    {
        $status = $request->query->get('status');
        $qb = $reviewRepository->createQueryBuilder('r');
        if ($status) {
            $qb->andWhere('r.status = :status')->setParameter('status', $status);
        }
        return $this->render('backoffice/reviews/index.html.twig', [
            'reviews' => $qb->getQuery()->getResult(),
            'filter' => $status,
        ]);
    }

    #[Route('/reviews/{id}/status', name: 'employee_reviews_status', methods: ['POST'])]
    public function status(Review $review, Request $request, EntityManagerInterface $em): Response
    {
        $review->setStatus($request->request->get('status'));
        $em->flush();
        $this->addFlash('success', 'Statut mis à jour.');
        return $this->redirectToRoute('employee_reviews');
    }
}
