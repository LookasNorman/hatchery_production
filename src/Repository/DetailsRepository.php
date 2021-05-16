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
class DetailsRepository extends ServiceEntityRepository
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
            ->join('eid.eggInput', 'e')
            ->orderBy('e.name')
            ->addOrderBy('eid.chicksRecipient')
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
            ->join('edd.eggsDeliveries', 'ed')
            ->join('ed.herd', 'h')
            ->where('eid.eggInput = :input')
            ->andWhere('h.breeder = :breeder')
            ->groupBy('eid')
            ->setParameters(['input' => $input, 'breeder' => $breeder])
            ->getQuery()
            ->getResult()
            ;
    }

    public function eggs()
    {
        return $this->createQueryBuilder('eid')
            ->select('SUM(idd.eggsNumber) as eggsNumber')
            ->leftJoin('eid.eggsSelections', 'es')
            ->leftJoin('eid.eggsInputsDetailsEggsDeliveries', 'idd')
            ->where('es.id IS NULL')
            ->getQuery()
            ->getSingleResult()
            ;
    }

    public function chicks()
    {
        return $this->createQueryBuilder('eid')
            ->select('SUM(eid.chickNumber) as chickNumber')
            ->leftJoin('eid.eggsSelections', 'es')
            ->where('es.id IS NULL')
            ->getQuery()
            ->getSingleResult()
            ;
    }
}
