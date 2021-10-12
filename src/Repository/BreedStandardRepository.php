<?php

namespace App\Repository;

use App\Entity\BreedStandard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BreedStandard|null find($id, $lockMode = null, $lockVersion = null)
 * @method BreedStandard|null findOneBy(array $criteria, array $orderBy = null)
 * @method BreedStandard[]    findAll()
 * @method BreedStandard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BreedStandardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BreedStandard::class);
    }

    // /**
    //  * @return BreedStandard[] Returns an array of BreedStandard objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BreedStandard
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
