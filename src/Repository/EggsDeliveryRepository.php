<?php

namespace App\Repository;

use App\Entity\EggsDelivery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EggsDelivery|null find($id, $lockMode = null, $lockVersion = null)
 * @method EggsDelivery|null findOneBy(array $criteria, array $orderBy = null)
 * @method EggsDelivery[]    findAll()
 * @method EggsDelivery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EggsDeliveryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EggsDelivery::class);
    }

    /**
     * @param $breeder
     * @return int|mixed|string
     */
    public function eggsOnWarehouse($breeder)
    {
        return $this->createQueryBuilder('ed')
            ->select('SUM(ed.eggsNumber) as eggsDeliveries', 'SUM(eid.eggsNumber) as eggsInputs')
            ->join('ed.herd', 'h')
            ->leftJoin('ed.eggsInputsDetailsEggsDeliveries', 'eid')
            ->where('h.breeder = :breeder')
            ->setParameter('breeder', $breeder)
            ->getQuery()
            ->getSingleResult()
            ;
    }

     /**
      * @return EggsDelivery[] Returns an array of EggsDelivery objects
      */
    public function findByExampleField($value): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?EggsDelivery
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
