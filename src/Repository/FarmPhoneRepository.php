<?php

namespace App\Repository;

use App\Entity\FarmPhone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FarmPhone|null find($id, $lockMode = null, $lockVersion = null)
 * @method FarmPhone|null findOneBy(array $criteria, array $orderBy = null)
 * @method FarmPhone[]    findAll()
 * @method FarmPhone[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FarmPhoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FarmPhone::class);
    }

    // /**
    //  * @return FarmPhone[] Returns an array of FarmPhone objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FarmPhone
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
