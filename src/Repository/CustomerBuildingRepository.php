<?php

namespace App\Repository;

use App\Entity\CustomerBuilding;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CustomerBuilding|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomerBuilding|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomerBuilding[]    findAll()
 * @method CustomerBuilding[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerBuildingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerBuilding::class);
    }

    // /**
    //  * @return CustomerBuilding[] Returns an array of CustomerBuilding objects
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
    public function findOneBySomeField($value): ?CustomerBuilding
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
