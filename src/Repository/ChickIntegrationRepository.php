<?php

namespace App\Repository;

use App\Entity\ChickIntegration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChickIntegration|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChickIntegration|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChickIntegration[]    findAll()
 * @method ChickIntegration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChickIntegrationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChickIntegration::class);
    }

    // /**
    //  * @return ChickIntegration[] Returns an array of ChickIntegration objects
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
    public function findOneBySomeField($value): ?ChickIntegration
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
