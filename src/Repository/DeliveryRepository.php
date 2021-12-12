<?php

namespace App\Repository;

use App\Entity\Delivery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
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

    public function monthlyDeliveredEgg($start, $end)
    {
        return $this->createQueryBuilder('d')
            ->select('YEARMONTH(d.deliveryDate) as month', 'SUM(d.eggsNumber) as eggNumber')
            ->andWhere('d.deliveryDate BETWEEN :start AND :end')
            ->setParameters(['start' => $start, 'end' => $end])
            ->orderBy('month', 'asc')
            ->groupBy('month')
            ->getQuery()
            ->getResult()
            ;
    }

    public function deliveryIndex()
    {
        return $this->createQueryBuilder('d')
            ->select('d.deliveryDate', 'd.firstLayingDate', 'd.lastLayingDate', 'd.partIndex', 'd.id', 'd.eggsOnWarehouse', 'd.eggsNumber')
            ->addSelect('d.eggsNumber - sum(COALESCE(id.eggsNumber,0)) as eggsStock')
            ->addSelect('(SELECT sum(COALESCE(se.eggsNumber,0))
            FROM App:SellingEgg se
            WHERE d.id = se.delivery) AS selledEgg')
            ->addSelect('h.name as herd', 'b.name as breeder')
            ->join('d.herd', 'h')
            ->join('h.breeder', 'b')
            ->leftJoin('d.inputDeliveries', 'id')
            ->orderBy('d.deliveryDate', 'desc')
            ->groupBy('d')
            ->getQuery()
            ->getResult();
    }

    public function herdDelivery($herd)
    {
        return $this->createQueryBuilder('d')
            ->join('d.inputDeliveries', 'id')
            ->leftJoin('id.lighting', 'l')
            ->leftJoin('id.selection', 's')
            ->andWhere('d.herd = :herd')
            ->setParameters(['herd' => $herd])
            ->addOrderBy('d.deliveryDate', 'asc')
            ->groupBy('d')
            ->getQuery()
            ->getResult();
    }

    public function herdDeliveryOnStock($herd)
    {
        return $this->createQueryBuilder('d')
            ->addSelect('id')
            ->leftJoin('d.inputDeliveries', 'id')
            ->leftJoin('d.sellingEggs', 'es')
            ->andWhere('d.herd = :herd')
            ->setParameters(['herd' => $herd])
            ->orderBy('d.deliveryDate', 'asc')
            ->getQuery()
            ->getResult();
    }

    public function herdDeliveryWithStock($herd)
    {
        return $this->createQueryBuilder('d')
            ->addSelect('d.eggsNumber - SUM(ifd.eggsNumber) as eggsStock')
            ->join('d.inputsFarmDeliveries', 'ifd')
            ->andWhere('d.herd = :herd')
            ->setParameters(['herd' => $herd])
            ->groupBy('d')
            ->orderBy('d.deliveryDate', 'desc')
            ->getQuery()
            ->getResult();
    }

    public function lastHerdDelivery($herd)
    {
        return $this->createQueryBuilder('ed')
            ->select('MAX(ed.deliveryDate)')
            ->andWhere('ed.herd = :herd')
            ->setParameters(['herd' => $herd])
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function eggsBreedDelivered($breed)
    {
        return $this->createQueryBuilder('ed')
            ->select('SUM(ed.eggsNumber) as eggsInWarehouse')
            ->join('ed.herd', 'h')
            ->andWhere('h.breed = :breed')
            ->setParameters(['breed' => $breed])
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function eggsDelivered()
    {
        return $this->createQueryBuilder('ed')
            ->select('SUM(ed.eggsNumber) as eggsInWarehouse')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function eggsInWarehouse()
    {
        return $this->createQueryBuilder('ed')
            ->select('SUM(ed.eggsOnWarehouse) as eggsInWarehouse')
            ->andWhere('ed.eggsOnWarehouse > 0')
            ->getQuery()
            ->getResult();
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
            ->getResult();
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
