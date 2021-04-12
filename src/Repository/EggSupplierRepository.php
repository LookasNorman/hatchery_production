<?php

namespace App\Repository;

use App\Entity\EggSupplier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EggSupplier|null find($id, $lockMode = null, $lockVersion = null)
 * @method EggSupplier|null findOneBy(array $criteria, array $orderBy = null)
 * @method EggSupplier[]    findAll()
 * @method EggSupplier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EggSupplierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EggSupplier::class);
    }

    // /**
    //  * @return EggSupplier[] Returns an array of EggSupplier objects
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
    public function findOneBySomeField($value): ?EggSupplier
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
