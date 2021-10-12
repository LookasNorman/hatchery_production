<?php

namespace App\Repository;

use App\Entity\PlanDeliveryEgg;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlanDeliveryEgg|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanDeliveryEgg|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanDeliveryEgg[]    findAll()
 * @method PlanDeliveryEgg[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanDeliveryEggRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanDeliveryEgg::class);
    }

    // /**
    //  * @return PlanDeliveryEgg[] Returns an array of PlanDeliveryEgg objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PlanDeliveryEgg
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
