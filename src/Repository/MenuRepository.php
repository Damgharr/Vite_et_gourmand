<?php

namespace App\Repository;

use App\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    public function findFiltered(array $filters): array
    {
        $qb = $this->createQueryBuilder('m')
            ->orderBy('m.id', 'ASC');

        $minPrice = $filters['minPrice'] ?? null;
        $maxPrice = $filters['maxPrice'] ?? null;
        $theme = $filters['theme'] ?? null;
        $diet = $filters['diet'] ?? null;
        $minPeople = $filters['minPeople'] ?? null;

        if ($minPrice !== '' && $minPrice !== null) {
            $qb->andWhere('m.pricePerPeople >= :minPrice')
               ->setParameter('minPrice', $minPrice);
        }

        if ($maxPrice !== '' && $maxPrice !== null) {
            $qb->andWhere('m.pricePerPeople <= :maxPrice')
               ->setParameter('maxPrice', $maxPrice);
        }

        if (!empty($theme)) {
            $qb->andWhere('m.theme = :theme')
               ->setParameter('theme', $theme);
        }

        if (!empty($diet)) {
            $qb->andWhere('m.diet = :diet')
               ->setParameter('diet', $diet);
        }

        if ($minPeople !== '' && $minPeople !== null) {
            $qb->andWhere('m.minPeopleAmount <= :minPeople')
               ->setParameter('minPeople', $minPeople);
        }

        return $qb->getQuery()->getResult();
    }
}
