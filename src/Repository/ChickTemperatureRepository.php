<?php

namespace App\Repository;

use App\Entity\ChickTemperature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChickTemperature|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChickTemperature|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChickTemperature[]    findAll()
 * @method ChickTemperature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChickTemperatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChickTemperature::class);
    }

    // /**
    //  * @return ChickTemperature[] Returns an array of ChickTemperature objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ChickTemperature
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
