<?php

namespace App\Repository;

use App\Entity\Inputs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Inputs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Inputs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Inputs[]    findAll()
 * @method Inputs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InputsRepository extends ServiceEntityRepository
{
//    public function herdDelivery()
//    {
//        return $this->createQueryBuilder('i')
//            ->join('i.inputsFarms', 'if')
//            ->join('if.inputsFarmDeliveries', 'ifd')
//            ->join('ifd.delivery' ,'d')
//            ->getQuery()
//            ->getResult()
//            ;
//    }


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inputs::class);
    }
}
