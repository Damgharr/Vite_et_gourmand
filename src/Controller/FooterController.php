<?php

namespace App\Controller;

use App\Repository\OpeningHoursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class FooterController extends AbstractController
{
    public function footer(OpeningHoursRepository $openingHoursRepository): Response
    {
        $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $hours = [];
        foreach ($days as $day) {
            $oh = $openingHoursRepository->findOneBy(['day' => $day]);
            if ($oh && $oh->getOpenTime() && $oh->getCloseTime()) {
                $hours[$day] = $oh->getOpenTime()->format('H:i') . ' - ' . $oh->getCloseTime()->format('H:i');
            } else {
                $hours[$day] = 'fermé';
            }
        }

        return $this->render('components/footer.html.twig', [
            'hours' => $hours,
        ]);
    }
}
