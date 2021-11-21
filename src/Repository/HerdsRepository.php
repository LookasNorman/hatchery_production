<?php

namespace App\Repository;

use App\Entity\Herds;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method Herds|null find($id, $lockMode = null, $lockVersion = null)
 * @method Herds|null findOneBy(array $criteria, array $orderBy = null)
 * @method Herds[]    findAll()
 * @method Herds[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HerdsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Herds::class);
    }

    public function hatchingDate()
    {
        $qb = $this->createQueryBuilder('h');
        return $qb
            ->select('h.hatchingDate')
            ->join('h.planDeliveryEggs', 'pde')
            ->groupBy('h.hatchingDate')
            ->orderBy('h.hatchingDate')
            ->getQuery()
            ->getResult();
    }

    public function herdInInput($input)
    {
        return $this->createQueryBuilder('h')
            ->join('h.eggsDeliveries', 'ed')
            ->join('ed.inputDeliveries', 'id')
            ->andWhere('id.input = :input')
            ->setParameter('input', $input)
            ->orderBy('h.name', 'ASC')
            ->groupBy('h')
            ->getQuery()
            ->getResult()
        ;
    }

}
