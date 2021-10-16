<?php

namespace App\Repository;

use App\Entity\Delivery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
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

    public function eggsBreedDelivered($breed)
    {
        return $this->createQueryBuilder('ed')
            ->select('SUM(ed.eggsNumber) as eggsInWarehouse')
            ->join('ed.herd', 'h')
            ->andWhere('h.breed = :breed')
            ->setParameters(['breed' => $breed])
            ->getQuery()
            ->getSingleScalarResult()
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

    public function deliveryOnWarehouse()
    {
        return $this->createQueryBuilder('ed')
            ->join('ed.herd', 'h')
            ->join('h.breeder', 'b')
            ->where('ed.eggsOnWarehouse > 0')
            ->orderBy('b.name')
            ->addOrderBy('h.name')
            ->addOrderBy('ed.deliveryDate')
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
