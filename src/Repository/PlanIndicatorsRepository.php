<?php

namespace App\Repository;

use App\Entity\PlanIndicators;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlanIndicators|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanIndicators|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanIndicators[]    findAll()
 * @method PlanIndicators[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanIndicatorsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanIndicators::class);
    }

    // /**
    //  * @return PlanIndicators[] Returns an array of PlanIndicators objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PlanIndicators
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
