<?php

namespace App\Repository;

use App\Entity\PlanDeliveryChick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineExtensions\Query\Mysql;

/**
 * @method PlanDeliveryChick|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanDeliveryChick|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanDeliveryChick[]    findAll()
 * @method PlanDeliveryChick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanDeliveryChickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanDeliveryChick::class);
    }

    public function planBetweenDate($start, $end)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.inputDate BETWEEN :start AND :end')
            ->setParameters(['start' => $start, 'end' => $end])
            ->orderBy('p.inputDate', 'asc')
            ->getQuery()
            ->getResult();
    }

    public function planFromDate($date)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.inputDate >= :date')
            ->setParameters(['date' => $date])
            ->orderBy('p.inputDate', 'asc')
            ->getQuery()
            ->getResult();
    }

    public function dateDelivery($date, $dateEnd)
    {
        return $this->createQueryBuilder('p')
            ->select('p.inputDate')
            ->andWhere('p.inputDate BETWEEN :date AND :dateEnd')
            ->setParameters(['date' => $date, 'dateEnd' => $dateEnd])
            ->groupBy('p.inputDate')
            ->orderBy('p.inputDate')
            ->getQuery()
            ->getResult();
    }

    public function planCustomerWeek($farm, $date, $dateEnd)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.chickFarm = :farm')
            ->andWhere('p.inputDate BETWEEN :date AND :dateEnd')
            ->setParameters(['farm' => $farm, 'date' => $date, 'dateEnd' => $dateEnd])
            ->getQuery()
            ->getResult();
    }

    public function planDeliveryByCustomer($customer)
    {
        return $this->createQueryBuilder('p')
            ->join('p.chickFarm', 'cf')
            ->andWhere('cf.customer = :customer')
            ->setParameters(['customer' => $customer])
            ->orderBy('p.deliveryDate', 'asc')
            ->getQuery()
            ->getResult();
    }

    public function planBreedBetweenDate($breed, $date, $dateEnd)
    {
        return $this->createQueryBuilder('p')
            ->select('SUM(p.chickNumber) as chicks')
            ->innerJoin('p.breed', 'b')
            ->andWhere('b = :breed')
            ->andWhere('p.inputDate BETWEEN :date AND :dateEnd')
            ->setParameters(['breed' => $breed, 'date' => $date, 'dateEnd' => $dateEnd])
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function planBreedFarm($breed, $now)
    {
        return $this->createQueryBuilder('p')
            ->addSelect('YEARWEEK(p.inputDate, 1) as weekYear')
            ->join('p.chickFarm', 'f')
            ->join('p.breed', 'b')
            ->andWhere('b = :breed')
            ->andWhere('p.inputDate > :date')
            ->setParameters(['breed' => $breed, 'date' => $now])
            ->orderBy('weekYear', 'asc')
            ->addOrderBy('f.name', 'asc')
            ->getQuery()
            ->getResult();
    }

    public function planBreedWeek($breed, $now)
    {
        return $this->createQueryBuilder('p')
            ->select('YEARWEEK(p.inputDate, 1) as weekYear', 'SUM(p.chickNumber) as chickNumber')
            ->join('p.breed', 'b')
            ->andWhere('b = :breed')
            ->andWhere('p.inputDate > :date')
            ->setParameters(['breed' => $breed, 'date' => $now])
            ->groupBy('weekYear')
            ->orderBy('weekYear', 'asc')
            ->getQuery()
            ->getResult();
    }

    public function planInputsInWeekCustomer($breed, $date, $dateEnd)
    {
        return $this->createQueryBuilder('p')
            ->select('WEEK(p.inputDate) as yearWeek', 'SUM(p.chickNumber) as chicks', 'cf.name', 'cf.id', 'c.name as customer')
            ->innerJoin('p.breed', 'b')
            ->innerJoin('p.chickFarm', 'cf')
            ->innerJoin('cf.customer', 'c')
            ->andWhere('b = :breed')
            ->andWhere('p.inputDate BETWEEN :date AND :dateEnd')
            ->setParameters(['breed' => $breed, 'date' => $date, 'dateEnd' => $dateEnd])
            ->orderBy('yearWeek', 'asc')
            ->groupBy('yearWeek')
            ->addGroupBy('p.chickFarm')
            ->getQuery()
            ->getResult();
    }

    public function planInputsDetail($start, $end, $breed)
    {
        return $this->createQueryBuilder('p')
            ->addSelect('WEEK(p.inputDate) as week')
            ->innerJoin('p.breed', 'b')
            ->andWhere('b = :breed')
            ->andWhere('p.inputDate BETWEEN :start AND :end')
            ->setParameters(['start' => $start, 'end' => $end, 'breed' => $breed])
            ->addOrderBy('p.inputDate', 'asc')
            ->getQuery()
            ->getResult();
    }

    public function planInputsInDay($start, $end, $breed)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.breed', 'b')
            ->andWhere('b = :breed')
            ->andWhere('p.inputDate BETWEEN :start AND :end')
            ->setParameters(['start' => $start, 'end' => $end, 'breed' => $breed])
            ->getQuery()
            ->getResult();
    }
}
