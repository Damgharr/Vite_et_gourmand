<?php

namespace App\Repository;

use App\Entity\OpeningHours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OpeningHoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OpeningHours::class);
    }

    public function findOrdered(): array
    {
        $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $results = $this->findAll();
        $ordered = [];

        foreach ($days as $day) {
            $found = null;
            foreach ($results as $r) {
                if ($r->getDay() === $day) {
                    $found = $r;
                    break;
                }
            }
            if (!$found) {
                $found = new OpeningHours();
                $found->setDay($day);
            }
            $ordered[] = $found;
        }

        return $ordered;
    }
}
