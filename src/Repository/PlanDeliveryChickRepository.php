<?php

namespace App\Repository;

use App\Entity\PlanDeliveryChick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlanDeliveryChick|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanDeliveryChick|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanDeliveryChick[]    findAll()
 * @method PlanDeliveryChick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanDeliveryChickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanDeliveryChick::class);
    }

    public function planInputsInDay($start, $end)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.inputDate BETWEEN :start AND :end')
            ->setParameters(['start' => $start, 'end' => $end])
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?PlanDeliveryChick
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
