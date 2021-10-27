<?php

namespace App\Repository;

use App\Entity\TransportList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TransportList|null find($id, $lockMode = null, $lockVersion = null)
 * @method TransportList|null findOneBy(array $criteria, array $orderBy = null)
 * @method TransportList[]    findAll()
 * @method TransportList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransportListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TransportList::class);
    }

    // /**
    //  * @return TransportList[] Returns an array of TransportList objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TransportList
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
