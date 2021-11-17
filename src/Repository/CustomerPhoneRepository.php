<?php

namespace App\Repository;

use App\Entity\CustomerPhone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CustomerPhone|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomerPhone|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomerPhone[]    findAll()
 * @method CustomerPhone[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerPhoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerPhone::class);
    }

    // /**
    //  * @return CustomerPhone[] Returns an array of CustomerPhone objects
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
    public function findOneBySomeField($value): ?CustomerPhone
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
