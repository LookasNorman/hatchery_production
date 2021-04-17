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
     * @param $herd
     * @return int|mixed|string
     */
    public function eggsOnWarehouse($herd)
    {
        return $this->createQueryBuilder('ed')
            ->where('ed.herd = :herd')
            ->andWhere('ed.eggsOnWarehouse > 0')
            ->setParameter('herd', $herd)
            ->getQuery()
            ->getResult()
            ;
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
