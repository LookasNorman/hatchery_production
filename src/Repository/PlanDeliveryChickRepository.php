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

}
