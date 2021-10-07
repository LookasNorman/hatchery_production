<?php

namespace App\Repository;

use App\Entity\PlanInputFarm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlanInputFarm|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanInputFarm|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanInputFarm[]    findAll()
 * @method PlanInputFarm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanInputFarmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanInputFarm::class);
    }

    // /**
    //  * @return PlanInputFarm[] Returns an array of PlanInputFarm objects
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
    public function findOneBySomeField($value): ?PlanInputFarm
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
