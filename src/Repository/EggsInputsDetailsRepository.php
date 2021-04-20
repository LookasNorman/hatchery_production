<?php

namespace App\Repository;

use App\Entity\EggsInputsDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EggsInputsDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method EggsInputsDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method EggsInputsDetails[]    findAll()
 * @method EggsInputsDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EggsInputsDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EggsInputsDetails::class);
    }

    public function deliveries()
    {
        return $this->createQueryBuilder('eid')
            ->addSelect('edd')
            ->join('eid.eggsInputsDetailsEggsDeliveries', 'edd')
            ->getQuery()
            ->execute();
    }

    public function deliveriesInput($val)
    {
        return $this->createQueryBuilder('eid')
            ->addSelect('edd')
            ->join('eid.eggsInputsDetailsEggsDeliveries', 'edd')
            ->where('eid.eggInput = :val')
            ->setParameter('val', $val)
            ->getQuery()
            ->execute();
    }

    // /**
    //  * @return EggsInputsDetails[] Returns an array of EggsInputsDetails objects
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
    public function findOneBySomeField($value): ?EggsInputsDetails
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
