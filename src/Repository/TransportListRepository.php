<?php

namespace App\Repository;

use App\Entity\TransportList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TransportList|null find($id, $lockMode = null, $lockVersion = null)
 * @method TransportList|null findOneBy(array $criteria, array $orderBy = null)
 * @method TransportList[]    findAll()
 * @method TransportList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransportListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TransportList::class);
    }

    public function transportList($start, $end)
    {
        return $this->createQueryBuilder('tl')
            ->join('tl.farm', 'f')
            ->join('f.eggInput', 'i')
            ->andWhere('i.inputDate BETWEEN :start AND :end')
            ->setParameters(['start' => $start, 'end' => $end])
            ->getQuery()
            ->getResult()
            ;
    }
}
