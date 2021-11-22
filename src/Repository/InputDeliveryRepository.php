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

    public function herdLightingEggsInInput($herd, $input)
    {
        return $this->createQueryBuilder('id')
            ->select('l.lightingDate', 'SUM(l.wasteLighting) as wasteLighting', 'SUM(l.lightingEggs) as lightingEggs', 'SUM(l.wasteEggs) as wasteEggs')
            ->addSelect('(1 - SUM(l.wasteEggs) / SUM(l.lightingEggs)) * 100 as fertilization')
            ->join('id.delivery', 'd')
            ->leftJoin('id.lighting', 'l')
            ->andWhere('d.herd = :herd')
            ->andWhere('id.input = :input')
            ->groupBy('l.lightingDate')
            ->setParameters(['input' => $input, 'herd' => $herd])
            ->getQuery()
            ->getSingleResult();
    }

    public function herdDelivery($herd)
    {
        return $this->createQueryBuilder('id')
            ->select('id.id', 'i.name', 'd.deliveryDate', 'id.eggsNumber', 'd.id as deliveryId')
            ->addSelect('l.wasteLighting', 't.transfersEgg', 's.chickNumber', 's.cullChicken', 's.unhatched')
            ->join('id.delivery', 'd')
            ->join('id.input', 'i')
            ->leftJoin('id.lighting', 'l')
            ->leftJoin('id.selection', 's')
            ->leftJoin('i.transfers', 't')
            ->andWhere('d.herd = :herd')
            ->setParameters(['herd' => $herd])
            ->orderBy('id.input')
            ->addOrderBy('d.deliveryDate', 'asc')
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

    public function eggsBreedProduction($breed)
    {
        return $this->createQueryBuilder('id')
            ->select('SUM(id.eggsNumber)')
            ->join('id.delivery', 'd')
            ->join('d.herd', 'h')
            ->andWhere('h.breed = :breed')
            ->setParameters(['breed' => $breed])
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }
}
