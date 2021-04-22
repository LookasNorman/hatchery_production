<?php

namespace App\Repository;

use App\Entity\EggsSelections;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EggsSelections|null find($id, $lockMode = null, $lockVersion = null)
 * @method EggsSelections|null findOneBy(array $criteria, array $orderBy = null)
 * @method EggsSelections[]    findAll()
 * @method EggsSelections[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EggsSelectionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EggsSelections::class);
    }

    // /**
    //  * @return EggsSelections[] Returns an array of EggsSelections objects
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
    public function findOneBySomeField($value): ?EggsSelections
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
