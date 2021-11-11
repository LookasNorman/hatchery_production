<?php

namespace App\Repository;

use App\Entity\InputDelivery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InputDelivery|null find($id, $lockMode = null, $lockVersion = null)
 * @method InputDelivery|null findOneBy(array $criteria, array $orderBy = null)
 * @method InputDelivery[]    findAll()
 * @method InputDelivery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InputDeliveryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InputDelivery::class);
    }

    public function eggsProduction()
    {
        return $this->createQueryBuilder('id')
            ->select('SUM(id.eggsNumber)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function eggsFromDelivery($delivery)
    {
        return $this->createQueryBuilder('id')
            ->select('SUM(id.eggsNumber)')
            ->andWhere('id.delivery = :delivery')
            ->setParameters(['delivery' => $delivery])
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function deliveryInLighting($lighting)
    {
        return $this->createQueryBuilder('id')
            ->andWhere('id.lighting = :lighting')
            ->setParameters(['lighting' => $lighting])
            ->getQuery()
            ->getArrayResult()
            ;
    }

    public function herdDeliveryInInputForLighting($herd, $input)
    {
        return $this->createQueryBuilder('id')
            ->join('id.delivery', 'd')
            ->andWhere('d.herd = :herd')
            ->andWhere('id.input = :input')
            ->setParameters(['input' => $input, 'herd' => $herd])
            ->getQuery()
            ->getResult();
    }

    public function herdInputEggsInInput($herd, $input)
    {
        return $this->createQueryBuilder('id')
            ->select('SUM(id.eggsNumber)')
            ->join('id.delivery', 'd')
            ->andWhere('d.herd = :herd')
            ->andWhere('id.input = :input')
            ->setParameters(['input' => $input, 'herd' => $herd])
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function herdDelivery($herd)
    {
        return $this->createQueryBuilder('id')
            ->join('id.delivery', 'd')
            ->andWhere('d.herd = :herd')
            ->setParameters(['herd' => $herd])
            ->orderBy('id.input')
            ->getQuery()
            ->getResult();
    }

    public function eggsInProduction()
    {
        return $this->createQueryBuilder('id')
            ->select('SUM(id.eggsNumber)')
            ->andWhere('id.selection IS EMPTY')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function wasteLightingInProduction()
    {
        return $this->createQueryBuilder('id')
            ->select('SUM(l.wasteEggs)')
            ->leftJoin('id.lighting', 'l')
            ->andWhere('id.selection IS EMPTY')
            ->getQuery()
            ->getSingleScalarResult();
    }

}
