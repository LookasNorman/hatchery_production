<?php

namespace App\Repository;

use App\Entity\InputsFarm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InputsFarm|null find($id, $lockMode = null, $lockVersion = null)
 * @method InputsFarm|null findOneBy(array $criteria, array $orderBy = null)
 * @method InputsFarm[]    findAll()
 * @method InputsFarm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InputsFarmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InputsFarm::class);
    }

    public function monthlyDeliveredChick($start, $end)
    {
        return $this->createQueryBuilder('if')
            ->select('YEARMONTH(i.inputDate) as month', 'SUM(if.chickNumber) as chickNumber')
            ->join('if.eggInput', 'i')
            ->andWhere('i.inputDate BETWEEN :start AND :end')
            ->setParameters(['start' => $start->modify('-21 days'), 'end' => $end->modify('-21 days')])
            ->orderBy('month', 'asc')
            ->groupBy('month')
            ->getQuery()
            ->getResult()
            ;
    }

    public function chickInInput($input)
    {
        return $this->createQueryBuilder('if')
            ->select('SUM(if.chickNumber)')
            ->andWhere('if.eggInput = :input')
            ->setParameters(['input' => $input])
            ->getQuery()
            ->getSingleScalarResult();
    }

}
