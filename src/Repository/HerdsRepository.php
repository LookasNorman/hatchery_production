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

    public function herdInInputFarm($farm)
    {
        return $this->createQueryBuilder('h')
            ->join('h.eggsDeliveries', 'ed')
            ->join('ed.inputsFarmDeliveries', 'ifd')
            ->andWhere('ifd.inputsFarm = :farm')
            ->setParameters(['farm' => $farm])
            ->getQuery()
            ->getResult()
            ;
    }

    public function herdInInput($input)
    {
        return $this->createQueryBuilder('h')
            ->join('h.eggsDeliveries', 'ed')
            ->join('ed.inputsFarmDeliveries', 'ifd')
            ->join('ifd.inputsFarm', 'if')
            ->andWhere('if.eggInput = :input')
            ->setParameter('input', $input)
            ->orderBy('h.name', 'ASC')
            ->groupBy('h')
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Herds
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
