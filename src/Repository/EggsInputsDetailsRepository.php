<?php

namespace App\Repository;

use App\Entity\InputsDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InputsDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method InputsDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method InputsDetails[]    findAll()
 * @method InputsDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EggsInputsDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InputsDetails::class);
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

    public function inputBreederDetails($input, $breeder)
    {
        return $this->createQueryBuilder('eid')
            ->addSelect('SUM(edd.eggsNumber)')
            ->join('eid.eggsInputsDetailsEggsDeliveries', 'edd')
            ->join('edd.EggsDeliveries', 'ed')
            ->join('ed.herd', 'h')
            ->where('eid.eggInput = :input')
            ->andWhere('h.breeder = :breeder')
            ->groupBy('eid')
            ->setParameters(['input' => $input, 'breeder' => $breeder])
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return InputsDetails[] Returns an array of InputsDetails objects
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
    public function findOneBySomeField($value): ?InputsDetails
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
