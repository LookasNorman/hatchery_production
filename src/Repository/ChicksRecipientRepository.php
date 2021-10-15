<?php

namespace App\Repository;

use App\Entity\ChicksRecipient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChicksRecipient|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChicksRecipient|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChicksRecipient[]    findAll()
 * @method ChicksRecipient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChicksRecipientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChicksRecipient::class);
    }

    public function inputsDelivery($recipient)
    {
        return $this->createQueryBuilder('cr')
            ->addSelect('if', 'ifd')
            ->join('cr.inputsFarms', 'if')
            ->join('if.inputsFarmDeliveries', 'ifd')
            ->where('cr.id = :recipient')
            ->setParameter('recipient', $recipient)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    // /**
    //  * @return ChicksRecipient[] Returns an array of ChicksRecipient objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ChicksRecipient
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
