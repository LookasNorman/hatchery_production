<?php

namespace App\Repository;

use App\Entity\EggsInputsDetailsEggsDelivery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EggsInputsDetailsEggsDelivery|null find($id, $lockMode = null, $lockVersion = null)
 * @method EggsInputsDetailsEggsDelivery|null findOneBy(array $criteria, array $orderBy = null)
 * @method EggsInputsDetailsEggsDelivery[]    findAll()
 * @method EggsInputsDetailsEggsDelivery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EggsInputsDetailsEggsDeliveryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EggsInputsDetailsEggsDelivery::class);
    }

    // /**
    //  * @return EggsInputsDetailsEggsDelivery[] Returns an array of EggsInputsDetailsEggsDelivery objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EggsInputsDetailsEggsDelivery
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
