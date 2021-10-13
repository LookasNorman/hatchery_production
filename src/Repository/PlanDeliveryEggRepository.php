<?php

namespace App\Repository;

use App\Entity\PlanDeliveryEgg;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlanDeliveryEgg|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanDeliveryEgg|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanDeliveryEgg[]    findAll()
 * @method PlanDeliveryEgg[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanDeliveryEggRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanDeliveryEgg::class);
    }

    public function planDeliveryInDay($start, $end)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.deliveryDate BETWEEN :start AND :end')
            ->setParameters(['start' => $start, 'end' => $end])
            ->getQuery()
            ->getResult()
            ;
    }

    public function herdPlanDeliveryInDay($start, $end, $herd)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.deliveryDate BETWEEN :start AND :end')
            ->andWhere('p.herd = :herd')
            ->setParameters(['start' => $start, 'end' => $end, 'herd' => $herd])
            ->getQuery()
            ->getResult()
            ;
    }

    /*
    public function findOneBySomeField($value): ?PlanDeliveryEgg
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
