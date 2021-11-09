<?php

namespace App\Repository;

use App\Entity\SellingEgg;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SellingEgg|null find($id, $lockMode = null, $lockVersion = null)
 * @method SellingEgg|null findOneBy(array $criteria, array $orderBy = null)
 * @method SellingEgg[]    findAll()
 * @method SellingEgg[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SellingEggRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SellingEgg::class);
    }

    public function sellingEggs()
    {
        return $this->createQueryBuilder('s')
            ->select('SUM(s.eggsNumber)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function eggsFromDelivery($delivery)
    {
        return $this->createQueryBuilder('s')
            ->select('SUM(s.eggsNumber)')
            ->andWhere('s.delivery = :delivery')
            ->setParameters(['delivery' => $delivery])
            ->getQuery()
            ->getSingleScalarResult();
    }
}
