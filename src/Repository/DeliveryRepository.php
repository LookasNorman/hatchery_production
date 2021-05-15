<?php

namespace App\Repository;

use App\Entity\Delivery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Delivery|null find($id, $lockMode = null, $lockVersion = null)
 * @method Delivery|null findOneBy(array $criteria, array $orderBy = null)
 * @method Delivery[]    findAll()
 * @method Delivery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeliveryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Delivery::class);
    }

    /**
     * @param $herd
     * @return int|mixed|string
     */
    public function eggsOnWarehouse($herd)
    {
        return $this->createQueryBuilder('ed')
            ->where('ed.herd = :herd')
            ->andWhere('ed.eggsOnWarehouse > 0')
            ->setParameter('herd', $herd)
            ->getQuery()
            ->getResult()
            ;
    }

    public function eggsInWarehouse()
    {
        return $this->createQueryBuilder('ed')
            ->select('SUM(ed.eggsOnWarehouse) as eggsInWarehouse')
            ->andWhere('ed.eggsOnWarehouse > 0')
            ->getQuery()
            ->getResult()
            ;
    }

    /*
    public function findOneBySomeField($value): ?Delivery
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
