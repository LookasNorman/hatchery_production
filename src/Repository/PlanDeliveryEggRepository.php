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

    public function planHatchingDate($date)
    {
        return $this->createQueryBuilder('p')
            ->select('SUM(p.eggsNumber) as eggsNumber', 'p.deliveryDate')
            ->join('p.herd', 'h')
            ->andWhere('h.hatchingDate = :date')
            ->setParameters(['date' => $date])
            ->groupBy('p.deliveryDate')
            ->getQuery()
            ->getResult();
    }

    public function planHatchingDateWeek($date)
    {
        return $this->createQueryBuilder('p')
            ->select('SUM(p.eggsNumber) as eggsNumber', 'YEARWEEK(p.deliveryDate) as weekYear')
            ->join('p.herd', 'h')
            ->andWhere('h.hatchingDate = :date')
            ->setParameters(['date' => $date])
            ->orderBy('weekYear', 'asc')
            ->groupBy('weekYear')
            ->getQuery()
            ->getResult();
    }

    public function planBreeder($breeder)
    {
        return $this->createQueryBuilder('p')
            ->select('SUM(p.eggsNumber) as eggsNumber', 'p.deliveryDate')
            ->join('p.herd', 'h')
            ->andWhere('h.breeder = :breeder')
            ->setParameters(['breeder' => $breeder])
            ->groupBy('p.deliveryDate')
            ->getQuery()
            ->getResult();
    }

    public function planBreederWeek($breeder)
    {
        return $this->createQueryBuilder('p')
            ->select('SUM(p.eggsNumber) as eggsNumber', 'YEARWEEK(p.deliveryDate) as weekYear')
            ->join('p.herd', 'h')
            ->andWhere('h.breeder = :breeder')
            ->setParameters(['breeder' => $breeder])
            ->orderBy('weekYear', 'asc')
            ->groupBy('weekYear')
            ->getQuery()
            ->getResult();
    }

    public function planBreedBeetwenDate($breed, $date, $dateEnd)
    {
        return $this->createQueryBuilder('p')
            ->select('SUM(p.eggsNumber) as eggs')
            ->innerJoin('p.herd', 'h')
            ->innerJoin('h.breed', 'b')
            ->andWhere('b = :breed')
            ->andWhere('p.deliveryDate BETWEEN :date AND :dateEnd')
            ->setParameters(['breed' => $breed, 'date' => $date, 'dateEnd' => $dateEnd])
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    public function planInputsInWeekHerd($breed, $date, $dateEnd)
    {
        return $this->createQueryBuilder('p')
            ->select('WEEK(p.deliveryDate) as yearWeek', 'SUM(p.eggsNumber) as eggs', 'h.name')
            ->innerJoin('p.herd', 'h')
            ->innerJoin('h.breed', 'b')
            ->andWhere('b = :breed')
            ->andWhere('p.deliveryDate BETWEEN :date AND :dateEnd')
            ->setParameters(['breed' => $breed, 'date' => $date, 'dateEnd' => $dateEnd])
            ->orderBy('yearWeek', 'asc')
            ->groupBy('yearWeek')
            ->addGroupBy('p.herd')
            ->getQuery()
            ->getResult()
            ;
    }

    public function planDeliveryDetails($start, $end, $breed)
    {
        return $this->createQueryBuilder('p')
            ->join('p.herd', 'h')
            ->andWhere('h.breed = :breed')
            ->andWhere('p.deliveryDate BETWEEN :start AND :end')
            ->setParameters(['start' => $start, 'end' => $end, 'breed' => $breed])
            ->addOrderBy('p.deliveryDate', 'asc')
            ->getQuery()
            ->getResult()
            ;
    }

    public function planDeliveryInDay($start, $end, $breed)
    {
        return $this->createQueryBuilder('p')
            ->join('p.herd', 'h')
            ->andWhere('h.breed = :breed')
            ->andWhere('p.deliveryDate BETWEEN :start AND :end')
            ->setParameters(['start' => $start, 'end' => $end, 'breed' => $breed])
            ->getQuery()
            ->getResult()
            ;
    }

    public function herdPlanDeliveryInDay($start, $end, $herd)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.deliveryDate BETWEEN :start AND :end')
            ->andWhere('p.herd = :herd')
            ->setParameters(['start' => $start, 'end' => $end, 'herd' => $herd])
            ->getQuery()
            ->getResult()
            ;
    }

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
