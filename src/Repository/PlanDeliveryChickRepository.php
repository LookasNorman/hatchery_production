<?php

namespace App\Repository;

use App\Entity\PlanDeliveryChick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

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

    public function planDeliveryByCustomer($customer)
    {
        return $this->createQueryBuilder('p')
            ->join('p.chickFarm', 'cf')
            ->andWhere('cf.customer = :customer')
            ->setParameters(['customer' => $customer])
            ->orderBy('p.inputDate')
            ->getQuery()
            ->getResult()
            ;
    }

    public function planInputsInDay($start, $end, $breed)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.breed', 'b')
            ->andWhere('b = :breed')
            ->andWhere('p.inputDate BETWEEN :start AND :end')
            ->setParameters(['start' => $start, 'end' => $end, 'breed' => $breed])
            ->getQuery()
            ->getResult()
        ;
    }
}
