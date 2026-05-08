<?php

namespace App\Repository;

use App\Document\FinishedOrder;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceDocumentRepository<FinishedOrder>
 */
class FinishedOrderRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FinishedOrder::class);
    }
}
