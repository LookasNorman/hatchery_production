<?php

namespace App\Repository;

use App\Entity\TransportInputsFarm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TransportInputsFarm|null find($id, $lockMode = null, $lockVersion = null)
 * @method TransportInputsFarm|null findOneBy(array $criteria, array $orderBy = null)
 * @method TransportInputsFarm[]    findAll()
 * @method TransportInputsFarm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransportInputsFarmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TransportInputsFarm::class);
    }

    // /**
    //  * @return TransportInputsFarm[] Returns an array of TransportInputsFarm objects
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
    public function findOneBySomeField($value): ?TransportInputsFarm
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
