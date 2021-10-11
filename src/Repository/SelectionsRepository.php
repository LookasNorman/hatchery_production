<?php

namespace App\Repository;

use App\Entity\Selections;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Selections|null find($id, $lockMode = null, $lockVersion = null)
 * @method Selections|null findOneBy(array $criteria, array $orderBy = null)
 * @method Selections[]    findAll()
 * @method Selections[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SelectionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Selections::class);
    }

    public function herdSelectionInInput($herd, $input)
    {
        return $this->createQueryBuilder('s')
            ->select('s.selectionDate')
            ->addSelect('SUM(s.chickNumber) as chickNumber')
            ->addSelect('SUM(s.cullChicken) as cullChicken')
            ->addSelect('SUM(s.unhatched) as unhatched')
            ->join('s.inputsFarmDelivery', 'ifd')
            ->join('ifd.delivery', 'd')
            ->join('ifd.inputsFarm', 'if')
            ->andWhere('d.herd = :herd')
            ->andWhere('if.eggInput = :input')
            ->setParameters(['herd' => $herd, 'input' => $input])
            ->groupBy('s.selectionDate')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Selections
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
