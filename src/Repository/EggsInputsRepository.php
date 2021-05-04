<?php

namespace App\Repository;

use App\Entity\EggsInputs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EggsInputs|null find($id, $lockMode = null, $lockVersion = null)
 * @method EggsInputs|null findOneBy(array $criteria, array $orderBy = null)
 * @method EggsInputs[]    findAll()
 * @method EggsInputs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EggsInputsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EggsInputs::class);
    }

    public function inputsDetails()
    {
        return $this->createQueryBuilder('e')
            ->select('SUM(eid.chickNumber) as chickNumber')
            ->join('e.eggsInputsDetails', 'eid')
            ->leftJoin('eid.eggsSelections', 'es')
            ->where('es.id IS NULL')
            ->getQuery()
            ->getScalarResult()
            ;
    }

    // /**
    //  * @return EggsInputs[] Returns an array of EggsInputs objects
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
    public function findOneBySomeField($value): ?EggsInputs
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
