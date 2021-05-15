<?php

namespace App\Repository;

use App\Entity\DetailsDelivery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DetailsDelivery|null find($id, $lockMode = null, $lockVersion = null)
 * @method DetailsDelivery|null findOneBy(array $criteria, array $orderBy = null)
 * @method DetailsDelivery[]    findAll()
 * @method DetailsDelivery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetailsDeliveryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailsDelivery::class);
    }

    // /**
    //  * @return DetailsDelivery[] Returns an array of DetailsDelivery objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DetailsDelivery
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
