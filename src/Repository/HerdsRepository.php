<?php

namespace App\Repository;

use App\Entity\Herds;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    public function herdsEggsOnWarehouse()
    {
        return $this->createQueryBuilder('h')
            ->addSelect('SUM(ed.eggsOnWarehouse) as eggs')
            ->join('h.eggsDeliveries', 'ed')
            ->groupBy('h')
            ->getQuery()
            ->getResult()
            ;
    }

}
