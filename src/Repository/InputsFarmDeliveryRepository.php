<?php

namespace App\Repository;

use App\Entity\InputsFarmDelivery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InputsFarmDelivery|null find($id, $lockMode = null, $lockVersion = null)
 * @method InputsFarmDelivery|null findOneBy(array $criteria, array $orderBy = null)
 * @method InputsFarmDelivery[]    findAll()
 * @method InputsFarmDelivery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InputsFarmDeliveryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InputsFarmDelivery::class);
    }

    public function inputFarmDeliveryForLighting($herd, $input)
    {
        return $this->createQueryBuilder('ifd')
            ->join('ifd.inputsFarm', 'if')
            ->join('ifd.delivery', 'd')
            ->andWhere('if.eggInput = :input')
            ->andWhere('d.herd = :herd')
            ->setParameters(['input' => $input, 'herd' => $herd])
            ->getQuery()
            ->getResult()
            ;
    }

    public function herdInputEggsInInput($herd, $input)
    {
        return $this->createQueryBuilder('ifd')
            ->select('SUM(ifd.eggsNumber)')
            ->join('ifd.delivery', 'd')
            ->join('ifd.inputsFarm', 'if')
            ->andWhere('d.herd = :herd')
            ->andWhere('if.eggInput = :input')
            ->setParameters(['input' => $input, 'herd' => $herd])
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    public function herdInputFarmDelivery($herd, $input)
    {
        return $this->createQueryBuilder('ifd')
            ->join('ifd.inputsFarm', 'if')
            ->join('ifd.delivery', 'd')
            ->andWhere('d.herd = :herd')
            ->andWhere('if.eggInput = :input')
            ->setParameters(['input' => $input, 'herd' => $herd])
            ->getQuery()
            ->getResult()
            ;
    }

    public function herdDelivery($herd)
    {
        return $this->createQueryBuilder('ifd')
            ->join('ifd.inputsFarm', 'if')
            ->join('ifd.delivery', 'd')
            ->andWhere('d.herd = :herd')
            ->setParameters(['herd' => $herd])
            ->getQuery()
            ->getResult()
            ;
    }
    /*
    public function findOneBySomeField($value): ?InputsFarmDelivery
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
