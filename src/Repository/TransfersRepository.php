<?php

namespace App\Repository;

use App\Entity\Transfers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Transfers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transfers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transfers[]    findAll()
 * @method Transfers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransfersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transfers::class);
    }

    public function herdTransferInInput($herd, $input)
    {
        return $this->createQueryBuilder('t')
            ->select('SUM(t.transfersEgg) as transferEgg')
            ->addSelect('t.transferDate')
            ->join('t.inputsFarmDelivery', 'ifd')
            ->join('ifd.delivery', 'd')
            ->join('ifd.inputsFarm', 'if')
            ->andWhere('d.herd = :herd')
            ->andWhere('if.eggInput = :input')
            ->setParameters(['herd' => $herd, 'input' => $input])
            ->groupBy('t.transferDate')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Transfers
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
