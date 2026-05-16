<?php

namespace App\Controller;

use App\Entity\OpeningHours;
use App\Repository\OpeningHoursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminOpeningHoursController extends AbstractController
{
    #[Route('/opening-hours', name: 'admin_opening_hours')]
    public function index(OpeningHoursRepository $openingHoursRepository): Response
    {
        return $this->render('admin/opening_hours/index.html.twig', [
            'hours' => $openingHoursRepository->findOrdered(),
        ]);
    }

    #[Route('/opening-hours/save', name: 'admin_opening_hours_save', methods: ['POST'])]
    public function save(Request $request, EntityManagerInterface $em, OpeningHoursRepository $openingHoursRepository): Response
    {
        $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $existing = $openingHoursRepository->findAll();
        $map = [];
        foreach ($existing as $e) {
            $map[$e->getDay()] = $e;
        }

        foreach ($days as $day) {
            $open = $request->request->get('open_' . $day);
            $close = $request->request->get('close_' . $day);

            if (isset($map[$day])) {
                $entry = $map[$day];
            } else {
                $entry = new OpeningHours();
                $entry->setDay($day);
                $em->persist($entry);
            }

            $entry->setOpenTime($open ? new \DateTime($open) : null);
            $entry->setCloseTime($close ? new \DateTime($close) : null);
        }

        $em->flush();
        $this->addFlash('success', 'Horaires mis à jour.');

        return $this->redirectToRoute('admin_opening_hours');
    }
}
