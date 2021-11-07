<?php

namespace App\Repository;

use App\Entity\PlanInput;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlanInput|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanInput|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanInput[]    findAll()
 * @method PlanInput[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanInputRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanInput::class);
    }

    // /**
    //  * @return PlanInput[] Returns an array of PlanInput objects
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
    public function findOneBySomeField($value): ?PlanInput
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
